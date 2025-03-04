var fix = 0;
$('.datepicker').datepicker();

document.getElementById('search-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const checkboxes = document.querySelectorAll('input[name="brand"]');
    const isChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);

    if (!isChecked) {
        alert("하나 이상의 브랜드를 선택해야 합니다.");
    }

    e.target.submit();
})