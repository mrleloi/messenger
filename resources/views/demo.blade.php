<div class="container my-5">
    <div class="col-12 col-lg-8 offset-lg-2">
        <div class="card rounded shadow">
            <div class="card-header">
                <span class="h4"><strong><i class="fas fa-users"></i> Demo Employees</strong></span>
            </div>
            <div id="available_acc_elm" class="card-body p-2">
                <div class="col-12 my-2 text-center"><div class="spinner-grow text-primary" role="status"></div></div>
            </div>
            <div class="card-header">
                <span class="h4"><strong><i class="fas fa-users"></i> Demo Admins</strong></span>
            </div>
            <div id="available_acc_elm_admin" class="card-body p-2">
                <div class="col-12 my-2 text-center"><div class="spinner-grow text-primary" role="status"></div></div>
            </div>
        </div>
    </div>
</div>
@push('special-js')
    <script>
        let demoLogin = function(email, type, token){
            Messenger.alert().Modal({
                size : 'sm',
                icon : 'sign-in-alt',
                pre_loader : true,
                centered : true,
                unlock_buttons : false,
                allow_close : false,
                backdrop_ctrl : false,
                title: 'Logging in',
                theme: 'primary'
            });
            Messenger.xhr().payload({
                route : '/login?type='+ type +'&token='+ token,
                success : function(e){
                    location.replace('/messenger');
                },
                fail_alert : true
            });
        };
        Messenger.xhr().request({
            route : '/demo-logins',
            success : function(data){
                $("#available_acc_elm").html(data.html);
                $("#available_acc_elm_admin").html(data.html_admin);
            },
            fail : null
        });

    </script>
@endpush
