<footer class="footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <script>
                    document.write(new Date().getFullYear())
                </script> © Online Tuition App.
            </div>
            <div class="col-sm-6">
                <div class="text-sm-end d-none d-sm-block">
                    <p>Online Tuition Platform</p>
                </div>
            </div>
        </div>
    </div>
</footer>
</div>
<!-- Styles for Notification Popup -->
<style>
    .notification-popup {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(0, 0, 0, 0.7);
        color: #fff;
        padding: 20px;
        width: 300px;
        border-radius: 12px;
        text-align: center;
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        animation: fadeIn 0.5s ease;
    }

    .notification-ok-btn {
        background-color: #ff4081;
        border: none;
        color: #fff;
        padding: 8px 16px;
        border-radius: 8px;
        cursor: pointer;
        margin-top: 12px;
        transition: background 0.3s;
    }

    .notification-ok-btn:hover {
        background-color: #e0356b;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translate(-50%, -60%);
        }
        to {
            opacity: 1;
            transform: translate(-50%, -50%);
        }
    }
</style>



<!-- Beautiful Notification Popup -->
<div id="notification-popup" class="notification-popup">
    <p id="notificationpopupdata"></p>
    <button onclick="closeNotification()" class="notification-ok-btn">OK</button>
    <audio id="notification-sound" src="{{ url('sounds/notification.mp3') }}" preload="auto"></audio>
</div>

<script>
    function showNotification(count) {
        var popup = document.getElementById('notification-popup');
        var sound = document.getElementById('notification-sound');
        document.getElementById('notificationpopupdata').innerHTML = 'You have ' + count + ' unread notifications';

        popup.style.display = 'block';
        sound.play();
    }

    function closeNotification() {
        var popup = document.getElementById('notification-popup');
        popup.style.display = 'none';
    }

    // Call the function to show the notification
    // showNotification();
</script>


<!-- JAVASCRIPT -->
@vite(['resources/sass/app.scss', 'resources/js/app.js'])
<script src="{{ url('new-styles/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ url('new-styles/assets/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ url('new-styles/assets/libs/node-waves/waves.min.js') }}"></script>
<script src="{{ url('new-styles/assets/libs/feather-icons/feather.min.js') }}"></script>
<script src="{{ url('new-styles/assets/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
<script src="{{ url('new-styles/assets/js/plugins.js') }}"></script>

<!-- apexcharts -->
<script src="{{ url('new-styles/assets/libs/apexcharts/apexcharts.min.js') }}"></script>

<!-- Vector map-->
<script src="{{ url('new-styles/assets/libs/jsvectormap/js/jsvectormap.min.js') }}"></script>
<script src="{{ url('new-styles/assets/libs/jsvectormap/maps/world-merc.js') }}"></script>

<!-- Dashboard init -->
<script src="{{ url('new-styles/assets/js/pages/dashboard-analytics.init.js') }}"></script>

<!-- App js -->
<script src="{{ url('new-styles/assets/js/app.js') }}"></script>

<!-- plugins:js -->
<script src="{{ url('vendors/js/vendor.bundle.base.js') }}"></script>
<!-- endinject -->
<!-- Plugin js for this page -->
<script src="{{ url('vendors/chart.js/Chart.min.js') }}"></script>
<script src="{{ url('vendors/datatables.net/jquery.dataTables.js') }}"></script>
<script src="{{ url('vendors/datatables.net-bs4/dataTables.bootstrap4.js') }}"></script>
<script src="{{ url('js/dataTables.select.min.js') }}"></script>
{{-- <script src="{{url('vendors/jquery/jquery.min.js')}}"></script> --}}


<!-- End plugin js for this page -->
<!-- inject:js -->
<script src="{{ url('js/off-canvas.js') }}"></script>
<script src="{{ url('js/hoverable-collapse.js') }}"></script>
<script src="{{ url('js/template.js') }}"></script>
<script src="{{ url('js/settings.js') }}"></script>
<script src="{{ url('js/todolist.js') }}"></script>
<!-- <script src="{{ url('js/ckeditor.js') }}"></script> -->
{{-- @vite(['resources/sass/app.scss', 'resources/js/app.js']) --}}
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

<!-- endinject -->
<!-- Custom js for this page-->
<script src="{{ url('js/jquery.cookie.js') }}" type="text/javascript"></script>
<script src="{{ url('js/dashboard.js') }}"></script>
<script src="{{ url('js/Chart.roundedBarCharts.js') }}"></script>
<!-- End custom js for this page-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
</body>

<script>
    // Function to fetch notifications and update unread count
function fetchNotificationsAndUpdateCount() {
    $.ajax({
        url: '/notifications', // Update this URL to your endpoint that fetches notifications
        type: 'GET',
        success: function(response) {
            var unreadCount = response.unread_count;
            var previousCount = sessionStorage.getItem('previousNotificationCount') || 0;

            // Check if the count has changed and show the popup only if it has
            if (unreadCount != previousCount) {
                sessionStorage.setItem('previousNotificationCount', unreadCount); // Update stored count

                // Show notification only if there are unread notifications
                if (unreadCount > 0) {
                    document.getElementById('notificationpopupdata').innerHTML = 'You have ' + unreadCount + ' unread notifications';
                    document.getElementById('notification-popup').style.display = 'block';
                    document.getElementById('notification-sound').play();
                }
            }

            // Update the badge count in the header
            $('#unreadNotificationCount').text(unreadCount);

            // Badge color update based on unread count
            if (unreadCount > 0) {
                $('#unreadNotificationCount').removeClass('bg-danger').addClass('bg-primary');
            } else {
                $('#unreadNotificationCount').removeClass('bg-primary').addClass('bg-danger');
            }

            // Clear previous notifications
            var notificationList = $('#all-noti-tab .pe-2');
            notificationList.empty();

            // Populate notifications in the list
            $.each(response.notifications, function(index, notification) {
                let createdAt = new Date(notification.created_at);
                let formattedDateTime = createdAt.toLocaleString();

                var notificationItem = `
                    <div class="text-reset notification-item d-block dropdown-item position-relative">
                        <div class="d-flex">
                            <div class="avatar-xs me-3 flex-shrink-0">
                                <span class="avatar-title bg-info-subtle rounded-circle fs-16">
                                    <img src="/images/students/profilepics/${notification.initiator_pic}" class="" onerror="this.onerror=null;this.src='https://mychoicetutor.com/images/avatar/default_avatar_img.jpg';">
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <a onclick="markAsRead(${notification.id})" href="/checkNotificationDetails/${notification.id}" class="stretched-link">
                                    <h6 class="mt-0 mb-2 lh-base">${notification.notification}</h6>
                                </a>
                                <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                    <span><i class="mdi mdi-clock-outline"></i> ${formattedDateTime} - ${notification.initiator_name} - (${notification.initiator_role})</span>
                                </p>
                            </div>
                            <div class="px-2 fs-15">
                                <div class="form-check notification-check">
                                    <input class="form-check-input" title="Mark as read" onclick="markAsRead('${notification.id}')" type="radio" value="" id="notification-check-${index}">
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                notificationList.append(notificationItem); // Append to All tab regardless of type
            });
        }
    });
}

// Function to close the notification popup
function closeNotification() {
    document.getElementById('notification-popup').style.display = 'none';
}

// Initial fetch and interval for updates
$(document).ready(function() {
    fetchNotificationsAndUpdateCount();
    setInterval(fetchNotificationsAndUpdateCount, 5000);
});
function markAsRead(notificationId) {
        $.ajax({
            url: '/markAsRead/' + notificationId,
            type: 'GET',
            success: function() {
                fetchNotificationsAndUpdateCount();
            }
        });
    }

    function checkNotificationDetails(notificationId) {
        $.ajax({
            url: '/checkNotificationDetails/' + notificationId,
            type: 'GET',
            success: function() {
                fetchNotificationsAndUpdateCount();
            }
        });
    }

</script>
</html>
