<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="dns-prefetch" href="https://code.jquery.com/">
        <link rel="dns-prefetch" href="https://cdn.jsdelivr.net/">

        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.11.0/css/all.css" crossorigin="anonymous">
        <title>@yield('title') | Тест право</title>
    </head>
    <body>
        @include('admin.layouts.head')
        @yield('content')
        @include('admin.layouts.foot')
        <script src="https://www.deniztezcan.me/js/tinymce/tinymce.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
        <script type="text/javascript">
            $(function () {
                $('[data-event="tooltip"]').tooltip();
                $('[data-action="destroy"]').on('click', function (e) {
                    e.preventDefault();
                    var r=confirm("{{trans('verwijderen')}}"); 
                    if(r==true) { 
                        $.ajax({
                            url: $(this).attr('href'),
                            type: 'delete',
                            data: {
                                _method: 'delete',
                                _token: $(this).data('token'),
                            }
                        }).always(function () {
                            location.reload();
                        });
                    }
                });
                $('select').on('click', function () {
                    $.each($(this).find('option'), function (key, value) {
                        $(value).removeAttr('selected');
                    });
                });
                $('.has-dropdown').on('click', function(){
                    // $(this).parent().find('.dropdown-menu').toggleClass('d-none');
                    $(this).parent().find('.dropdown-menu').toggleClass('d-none');
                });
            });
            tinymce.init({
                selector: '.summernote',
                height: 250,
                plugins: "link lists link table advtable hr wordcount code",
                toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link hr paste wordcount",
                paste_data_images: true
            });
        </script>
        @yield('scripts')
    </body>
</html>