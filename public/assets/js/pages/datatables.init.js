/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!***********************************************!*\
  !*** ./resources/js/pages/datatables.init.js ***!
  \***********************************************/
/*
Template Name: Minible - Admin & Dashboard Template
Author: Themesbrand
Website: https://themesbrand.com/
Contact: themesbrand@gmail.com
File: Datatables Js File
*/
$(document).ready(function () {
  $('#datatable').DataTable({

  }); //Buttons examples

  var table = $('#datatable-buttons').DataTable({
    pageLength: 15,
    lengthChange: false,
    buttons: ['excel', 'colvis'],
    order:[[3,'desc']]
  });
  table.buttons().container().appendTo('#datatable-buttons_wrapper .col-md-6:eq(0)');
  $(".dataTables_length select").addClass('form-select form-select-sm');
});
/******/ })()
;