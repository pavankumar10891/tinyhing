<?php ($segment = Request::segment(1)); ?>
<div class="sidebar ">
            <div class="items py-md-5 ">
                <ul>
                    <li class="<?php echo e(($segment == 'dashboard'  )? 'active': ''); ?>">
                        <a href="<?php echo e(route('user.nannydashboard')); ?>">
                            <svg aria-hidden="true" focusable="false" data-prefix="fas"
                                data-icon="tachometer-alt-fastest" role="img" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 576 512" class="svg-inline--fa fa-tachometer-alt-fastest fa-w-18 fa-2x">
                                <path fill="currentColor"
                                    d="M288 32C128.94 32 0 160.94 0 320c0 52.8 14.25 102.26 39.06 144.8 5.61 9.62 16.3 15.2 27.44 15.2h443c11.14 0 21.83-5.58 27.44-15.2C561.75 422.26 576 372.8 576 320c0-159.06-128.94-288-288-288zm144 128c17.67 0 32 14.33 32 32s-14.33 32-32 32-32-14.33-32-32 14.33-32 32-32zM288 96c17.67 0 32 14.33 32 32s-14.33 32-32 32-32-14.33-32-32 14.33-32 32-32zM96 384c-17.67 0-32-14.33-32-32s14.33-32 32-32 32 14.33 32 32-14.33 32-32 32zm48-160c-17.67 0-32-14.33-32-32s14.33-32 32-32 32 14.33 32 32-14.33 32-32 32zm339.95 151.67l-133.93 22.32c-1.51 6.37-3.69 12.48-6.9 18.01H232.88c-5.5-9.45-8.88-20.28-8.88-32 0-35.35 28.65-64 64-64 23.06 0 43.1 12.31 54.37 30.61l133.68-22.28c13.09-2.17 25.45 6.64 27.62 19.72 2.19 13.07-6.65 25.45-19.72 27.62z"
                                    class=""></path>
                            </svg>
                            Dashboard
                        </a>
                    </li>
                    <li class="<?php echo e(($segment == 'set-availability'  )? 'active': ''); ?>">
                        <a href="<?php echo e(route('user.set.availability')); ?>">
                            <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="bell-on" role="img"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"
                                class="svg-inline--fa fa-bell-on fa-w-20 fa-2x">
                                <path fill="currentColor"
                                    d="M112,192a24,24,0,0,0-24-24H24a24,24,0,0,0,0,48H88A24,24,0,0,0,112,192ZM51.66,64.78l55.42,32a24,24,0,1,0,24-41.56l-55.42-32a24,24,0,1,0-24,41.56ZM520.94,100a23.8,23.8,0,0,0,12-3.22l55.42-32a24,24,0,0,0-24-41.56l-55.42,32a24,24,0,0,0,12,44.78ZM320,512a64,64,0,0,0,64-64H256A64,64,0,0,0,320,512ZM616,168H552a24,24,0,0,0,0,48h64a24,24,0,0,0,0-48ZM479.92,208c0-77.69-54.48-139.91-127.94-155.16V32a32,32,0,1,0-64,0V52.84C214.56,68.09,160.08,130.31,160.08,208c0,102.31-36.14,133.53-55.47,154.28A31.28,31.28,0,0,0,96,384c.11,16.41,13,32,32.09,32H511.91c19.11,0,32-15.59,32.09-32a31.23,31.23,0,0,0-8.61-21.72C516.06,341.53,479.92,310.31,479.92,208Z"
                                    class=""></path>
                            </svg>
                            Set availability
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('user.nannyNotificationList')); ?>">
                            <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="bell-on" role="img"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"
                                class="svg-inline--fa fa-bell-on fa-w-20 fa-2x">
                                <path fill="currentColor"
                                    d="M112,192a24,24,0,0,0-24-24H24a24,24,0,0,0,0,48H88A24,24,0,0,0,112,192ZM51.66,64.78l55.42,32a24,24,0,1,0,24-41.56l-55.42-32a24,24,0,1,0-24,41.56ZM520.94,100a23.8,23.8,0,0,0,12-3.22l55.42-32a24,24,0,0,0-24-41.56l-55.42,32a24,24,0,0,0,12,44.78ZM320,512a64,64,0,0,0,64-64H256A64,64,0,0,0,320,512ZM616,168H552a24,24,0,0,0,0,48h64a24,24,0,0,0,0-48ZM479.92,208c0-77.69-54.48-139.91-127.94-155.16V32a32,32,0,1,0-64,0V52.84C214.56,68.09,160.08,130.31,160.08,208c0,102.31-36.14,133.53-55.47,154.28A31.28,31.28,0,0,0,96,384c.11,16.41,13,32,32.09,32H511.91c19.11,0,32-15.59,32.09-32a31.23,31.23,0,0,0-8.61-21.72C516.06,341.53,479.92,310.31,479.92,208Z"
                                    class=""></path>
                            </svg>
                            Notification
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('user.nannyInboxList')); ?>">
                            <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="envelope" role="img"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                                class="svg-inline--fa fa-envelope fa-w-16 fa-2x">
                                <path fill="currentColor"
                                    d="M502.3 190.8c3.9-3.1 9.7-.2 9.7 4.7V400c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V195.6c0-5 5.7-7.8 9.7-4.7 22.4 17.4 52.1 39.5 154.1 113.6 21.1 15.4 56.7 47.8 92.2 47.6 35.7.3 72-32.8 92.3-47.6 102-74.1 131.6-96.3 154-113.7zM256 320c23.2.4 56.6-29.2 73.4-41.4 132.7-96.3 142.8-104.7 173.4-128.7 5.8-4.5 9.2-11.5 9.2-18.9v-19c0-26.5-21.5-48-48-48H48C21.5 64 0 85.5 0 112v19c0 7.4 3.4 14.3 9.2 18.9 30.6 23.9 40.7 32.4 173.4 128.7 16.8 12.2 50.2 41.8 73.4 41.4z"
                                    class=""></path>
                            </svg>
                            Inbox
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('user.nannyBookingList')); ?>">
                            <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="calendar-alt"
                                role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"
                                class="svg-inline--fa fa-calendar-alt fa-w-14 fa-2x">
                                <path fill="currentColor"
                                    d="M0 464c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V192H0v272zm320-196c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12h-40c-6.6 0-12-5.4-12-12v-40zm0 128c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12h-40c-6.6 0-12-5.4-12-12v-40zM192 268c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12h-40c-6.6 0-12-5.4-12-12v-40zm0 128c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12h-40c-6.6 0-12-5.4-12-12v-40zM64 268c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12H76c-6.6 0-12-5.4-12-12v-40zm0 128c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12H76c-6.6 0-12-5.4-12-12v-40zM400 64h-48V16c0-8.8-7.2-16-16-16h-32c-8.8 0-16 7.2-16 16v48H160V16c0-8.8-7.2-16-16-16h-32c-8.8 0-16 7.2-16 16v48H48C21.5 64 0 85.5 0 112v48h448v-48c0-26.5-21.5-48-48-48z"
                                    class=""></path>
                            </svg>
                            My Bookings
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('user.nannyEarningList')); ?>">
                            <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="wallet" role="img"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                                class="svg-inline--fa fa-wallet fa-w-16 fa-3x">
                                <path fill="currentColor"
                                    d="M461.2 128H80c-8.84 0-16-7.16-16-16s7.16-16 16-16h384c8.84 0 16-7.16 16-16 0-26.51-21.49-48-48-48H64C28.65 32 0 60.65 0 96v320c0 35.35 28.65 64 64 64h397.2c28.02 0 50.8-21.53 50.8-48V176c0-26.47-22.78-48-50.8-48zM416 336c-17.67 0-32-14.33-32-32s14.33-32 32-32 32 14.33 32 32-14.33 32-32 32z"
                                    class=""></path>
                            </svg>
                            My Earning
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('user.nannyRatingList')); ?>">
                            <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="star" role="img"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"
                                class="svg-inline--fa fa-star fa-w-18 fa-2x">
                                <path fill="currentColor"
                                    d="M259.3 17.8L194 150.2 47.9 171.5c-26.2 3.8-36.7 36.1-17.7 54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 33.7L288 439.6l130.7 68.7c23.2 12.2 50.9-7.4 46.4-33.7l-25-145.5 105.7-103c19-18.5 8.5-50.8-17.7-54.6L382 150.2 316.7 17.8c-11.7-23.6-45.6-23.9-57.4 0z"
                                    class=""></path>
                            </svg>
                            Reviews and Ratings
                        </a>
                    </li>
                    <li class="<?php echo e(($segment == 'interviews'  )? 'active': ''); ?>">
                        <a href="<?php echo e(route('user.nannyInterviewList')); ?>">
                            <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="check-square"
                                role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"
                                class="svg-inline--fa fa-check-square fa-w-14 fa-2x">
                                <path fill="currentColor"
                                    d="M400 480H48c-26.51 0-48-21.49-48-48V80c0-26.51 21.49-48 48-48h352c26.51 0 48 21.49 48 48v352c0 26.51-21.49 48-48 48zm-204.686-98.059l184-184c6.248-6.248 6.248-16.379 0-22.627l-22.627-22.627c-6.248-6.248-16.379-6.249-22.628 0L184 302.745l-70.059-70.059c-6.248-6.248-16.379-6.248-22.628 0l-22.627 22.627c-6.248 6.248-6.248 16.379 0 22.627l104 104c6.249 6.25 16.379 6.25 22.628.001z"
                                    class=""></path>
                            </svg>
                            Interviews
                        </a>
                    </li>
                    <li class="<?php echo e(($segment == 'profile'  )? 'active': ''); ?>">
                        <a href="<?php echo e(route('nanny.edit.profile')); ?>">
                            <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="user" role="img"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"
                                class="svg-inline--fa fa-user fa-w-14 fa-2x">
                                <path fill="currentColor"
                                    d="M224 256c70.7 0 128-57.3 128-128S294.7 0 224 0 96 57.3 96 128s57.3 128 128 128zm89.6 32h-16.7c-22.2 10.2-46.9 16-72.9 16s-50.6-5.8-72.9-16h-16.7C60.2 288 0 348.2 0 422.4V464c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48v-41.6c0-74.2-60.2-134.4-134.4-134.4z"
                                    class=""></path>
                            </svg>
                            My Profile
                        </a>
                    </li>
                    <li>
                        <a href="javascript:void(0);">
                            <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="pen-square"
                                role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"
                                class="svg-inline--fa fa-pen-square fa-w-14 fa-2x">
                                <path fill="currentColor"
                                    d="M400 480H48c-26.5 0-48-21.5-48-48V80c0-26.5 21.5-48 48-48h352c26.5 0 48 21.5 48 48v352c0 26.5-21.5 48-48 48zM238.1 177.9L102.4 313.6l-6.3 57.1c-.8 7.6 5.6 14.1 13.3 13.3l57.1-6.3L302.2 242c2.3-2.3 2.3-6.1 0-8.5L246.7 178c-2.5-2.4-6.3-2.4-8.6-.1zM345 165.1L314.9 135c-9.4-9.4-24.6-9.4-33.9 0l-23.1 23.1c-2.3 2.3-2.3 6.1 0 8.5l55.5 55.5c2.3 2.3 6.1 2.3 8.5 0L345 199c9.3-9.3 9.3-24.5 0-33.9z"
                                    class=""></path>
                            </svg>
                            Payment Setting
                        </a>
                    </li>

                   
        
                    <li>
                        <a href="javascript:void(0);">
                            <svg aria-hidden="true" focusable="false" data-prefix="fad" data-icon="volume-up" role="img"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"
                                class="svg-inline--fa fa-volume-up fa-w-18 fa-2x">
                                <g class="fa-group">
                                    <path fill="currentColor"
                                        d="M0 328V184a24 24 0 0 1 24-24h102.06l89-88.95c15-15 41-4.49 41 17V424c0 21.44-25.94 32-41 17l-89-88.95H24A24 24 0 0 1 0 328z"
                                        class="fa-secondary"></path>
                                    <path fill="currentColor"
                                        d="M338.23 179.13a24 24 0 1 0-23.16 42.06 39.42 39.42 0 0 1 0 69.62 24 24 0 1 0 23.16 42.06 87.43 87.43 0 0 0 0-153.74zM480 256a184.64 184.64 0 0 0-85.77-156.24 23.9 23.9 0 0 0-33.12 7.46 24.29 24.29 0 0 0 7.41 33.36 136.67 136.67 0 0 1 0 230.84 24.28 24.28 0 0 0-7.41 33.36 23.94 23.94 0 0 0 33.12 7.46A184.62 184.62 0 0 0 480 256zM448.35 20a24.2 24.2 0 1 0-26.56 40.46 233.65 233.65 0 0 1 0 391.16A24.2 24.2 0 1 0 448.35 492a282 282 0 0 0 0-472.07z"
                                        class="fa-primary"></path>
                                </g>
                            </svg>
                            Admin Help & Support
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('user.logout')); ?>">
                            <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="sign-out-alt"
                                role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                                class="svg-inline--fa fa-sign-out-alt fa-w-16 fa-2x">
                                <path fill="currentColor"
                                    d="M497 273L329 441c-15 15-41 4.5-41-17v-96H152c-13.3 0-24-10.7-24-24v-96c0-13.3 10.7-24 24-24h136V88c0-21.4 25.9-32 41-17l168 168c9.3 9.4 9.3 24.6 0 34zM192 436v-40c0-6.6-5.4-12-12-12H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h84c6.6 0 12-5.4 12-12V76c0-6.6-5.4-12-12-12H96c-53 0-96 43-96 96v192c0 53 43 96 96 96h84c6.6 0 12-5.4 12-12z"
                                    class=""></path>
                            </svg>
                            Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div><?php /**PATH C:\xampp\htdocs\tinyhugs\resources\views/front/dashboard/elements/nanny_sidebar.blade.php ENDPATH**/ ?>