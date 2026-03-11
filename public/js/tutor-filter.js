
function redirect() {
    window.location.href = "{{('/student/register')}}";
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.advSearchForm .cancelBtn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            this.closest('form').reset();
            $('#collapseTwo').collapse('hide');
        });
    });
});
