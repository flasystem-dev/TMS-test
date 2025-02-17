<?php
namespace App\QueryBuilders\Vendor;

use App\Models\Vendor;
use Carbon\Carbon;

use App\QueryBuilders\Vendor\VendorQueryBuilder;
use App\QueryBuilders\Vendor\AdjustmentOrderQueryBuilder;
use App\QueryBuilders\Order\OrderDataQueryBuilder;
use App\QueryBuilders\Vendor\AdjustmentOrderQueryBilder;
use Illuminate\Support\Facades\DB;

class AdjustmentQueryBuilder
{

    public static function vendorSubQuery($brand, $year, $month, $idxes, $column, $word)
    {
        $vendorSubQuery = VendorQueryBuilder::createQuery();

        $vendorSubQuery = VendorQueryBuilder::vendorSubSelect($vendorSubQuery);

        $vendorSubQuery = VendorQueryBuilder::findBrand($vendorSubQuery, $brand);

        $vendorSubQuery = VendorQueryBuilder::isJungsan($vendorSubQuery);

        $vendorSubQuery = VendorQueryBuilder::enabledVendor($vendorSubQuery, $year, $month);

        $vendorSubQuery = VendorQueryBuilder::checkRegisteredDate($vendorSubQuery, $year, $month);

        // 명세서 선택을 위한 vendor 선택 추가
        if(!empty($idxes)) {
            $vendorSubQuery = VendorQueryBuilder::selectedVendor($vendorSubQuery, $idxes);
        }

        if(!$word) {
            $vendorSubQuery = VendorQueryBuilder::searchWord($vendorSubQuery, $column , $word);
        }

        return $vendorSubQuery;
    }

    public static function orderSubQuery($brand, $year, $month)
    {
        $orderSubQuery = OrderDataQueryBuilder::createDBQuery();

        $orderSubQuery = AdjustmentOrderQueryBilder::joinDeliveryWithWhere($orderSubQuery, $year, $month);

        $orderSubQuery = AdjustmentOrderQueryBilder::orderSubSelect($orderSubQuery);

        $orderSubQuery = OrderDataQueryBuilder::findBrand($orderSubQuery, $brand);

        $orderSubQuery = OrderDataQueryBuilder::canView($orderSubQuery);

        return AdjustmentOrderQueryBilder::orderGroupBy($orderSubQuery);
    }

    public static function itemSubQuery()
    {
        $itemSubQuery = AdjustmentOrderQueryBilder::createItemDBQuery();

        $itemSubQuery = AdjustmentOrderQueryBilder::itemSubSelect($itemSubQuery);

        return AdjustmentOrderQueryBilder::itemGroupBy($itemSubQuery);
    }



    public static function adjustmentQuery($vendorSubQuery, $orderSubQuery, $itemSubQuery, $year, $month)
    {
        $query = Vendor::Query()
            ->fromSub($vendorSubQuery, 'vendor')
            ->leftJoinSub($orderSubQuery, 'order_data', 'order_data.mall_code', '=', 'vendor.idx')
            ->leftJoinSub($itemSubQuery,'order_items', 'order_data.order_idx', '=', 'order_items.order_idx');

        $query = self::joinTotalInformation($query, $year, $month);

        $query = self::joinETCData($query, $year, $month);

        $query = self::joinSpecification($query, $year, $month);

        $query = self::vendorSelect($query);

        $query = self::orderItemSelect($query);

        $query = self::totalInformationETCSelct($query);

        $query = self::specificationSelect($query);


        return $query -> groupBy('vendor.idx')
            ->orderBy('order_cnt', 'desc')
            ->get();
    }

########################################################################################################################
    ###########################################  정산 - 월 단위 정산  #####################################################

    public static function joinTotalInformation($query, $year, $month)
    {
        $query -> leftJoin('total_information', function($join) use ($year, $month) {
            $join->on('vendor.idx', '=', 'total_information.mall_code')
                ->where('date_type', '=', 'delivery')
                ->where('total_information.year', '=', $year)
                ->where('total_information.month', '=', $month);
        });
        return $query;
    }

    public static function joinETCData($query, $year, $month)
    {
        $query ->leftJoin('mall_monthly_etc_price', function($join) use ($year, $month) {
            $join->on('vendor.idx', '=', 'mall_monthly_etc_price.mall_code')
                ->where('mall_monthly_etc_price.year', '=', $year)
                ->where('mall_monthly_etc_price.month', '=', $month);
        });
        return $query;
    }

    public static function joinSpecification($query, $year, $month)
    {
        $query ->leftJoin('specification', function($join) use ($year, $month) {
            $join->on('vendor.idx', '=', 'specification.mall_code')
                ->where('specification.year', '=', $year)
                ->where('specification.month', '=', $month);
        });
        return $query;
    }

    public static function vendorSelect($query)
    {
        $query -> addSelect(
            'vendor.idx as idx',                             'vendor.brand_type as brand_type',
            'vendor.mall_name as mall_name',                 'vendor.rep_name as rep_name',
            'vendor.partner_name as partner_name',           'vendor.rep_type as rep_type',
            'vendor.registered_date as registered_date',     'vendor.service_price as service_price',
            'vendor.service_percent as service_percent',     'vendor.bank_code as bank_code',
            'vendor.vendor_id as vendor_id',                 'vendor.gen_number as gen_number',
            'vendor.bank_number as bank_number',             'vendor.name_of_deposit as name_of_deposit'
        );
        return $query;
    }

    public static function orderItemSelect($query)
    {
        $query -> addSelect(
            'order_data.mall_code as mall_code',
            DB::raw('IFNULL(COUNT(order_data.order_idx), 0) as order_cnt'),
            DB::raw('IFNULL(SUM(order_data.total_amount), 0) as total_amount'),
            DB::raw('IFNULL(SUM(order_data.vendor_amount), 0) as vendor_amount'),
            DB::raw('IFNULL(SUM(order_items.vendor_options_amount), 0) as vendor_options_amount'),
            DB::raw('IFNULL(SUM(order_data.misu_amount), 0) as misu_amount')
        );
        return $query;
    }

    public static function totalInformationETCSelct($query)
    {
        $query -> addSelect(
            DB::raw('IFNULL(total_information.card_amount, 0) as card_amount'),
            'mall_monthly_etc_price.etc1 as etc1',            'mall_monthly_etc_price.etc2 as etc2',
            'mall_monthly_etc_price.etc3 as etc3',
        );
        return $query;
    }

    public static function specificationSelect($query)
    {
        $query -> addSelect(
            'specification.sp_order_cnt as sp_order_cnt',               'specification.sp_order_amount as sp_order_amount',
            'specification.sp_vendor_amount as sp_vendor_amount',       'specification.sp_vendor_options_amount as sp_vendor_options_amount',
            'specification.sp_service_percent as sp_service_percent',   'specification.sp_card_amount as sp_card_amount',
            'specification.sp_card_fee as sp_card_fee',                 'specification.sp_tax_amount as sp_tax_amount',
            'specification.sp_etc1 as sp_etc1',
            'specification.sp_etc2 as sp_etc2',                         'specification.sp_etc3 as sp_etc3',
            'specification.deposit_date',                               'specification.checkout_date as checkout_date',
            'specification.sp_service_fee as sp_service_fee',           'specification.sp_settlement_amount as sp_settlement_amount',
            'specification.id as sp_id',                                'specification.sp_deposit_price as sp_deposit_price'
        );
        return $query;
    }
}