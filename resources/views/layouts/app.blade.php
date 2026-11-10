<!doctype html>
@php
    $settings = settings();

@endphp
<html lang="en">
<!-- [Head] start -->
@include('admin.head')

<!-- [Head] end -->
<!-- [Body] Start -->

<body
    data-pc-preset="{{ !empty($settings['color_type']) && $settings['color_type'] == 'custom' ? 'custom' : $settings['accent_color'] }}"
    data-pc-sidebar-theme="light" data-pc-sidebar-caption="{{ $settings['sidebar_caption'] }}"
    data-pc-direction="{{ $settings['theme_layout'] }}" data-pc-theme="{{ $settings['theme_mode'] }}">
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <!-- [ Pre-loader ] End -->
    <!-- [ Sidebar Menu ] start -->
    @include('admin.menu')
    <!-- [ Sidebar Menu ] end -->
    <!-- [ Header Topbar ] start -->
    @include('admin.header')
    <!-- [ Header ] end -->
    <!-- [ Main Content ] start -->
    <div class="pc-container">
        <div class="pc-content">
            <!-- [ breadcrumb ] start -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="page-header-title">
                                <h5 class="m-b-10"> @yield('page-title')</h5>
                            </div>
                        </div>
                        <div class="col-auto">
                            <ul class="breadcrumb">
                                @yield('breadcrumb')
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

           
            <!-- [ breadcrumb ] end -->


            <!-- [ Main Content ] start -->
            @include('admin.content')

            <!-- [ Main Content ] end -->
        </div>
    </div>

    <!-- [ Main Content ] end -->
    @include('admin.footer')

    <div class="modal fade" id="customModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="body">
                </div>
            </div>
        </div>
    </div>

    <!-- Reorder Selected Options to Top of Multiple Select boxes dynamically -->
    <script>
        (function() {
            function sortSelectMultiple() {
                var selects = document.querySelectorAll('select[multiple]');
                selects.forEach(function(select) {
                    if (!select.dataset.sortedInit) {
                        var selectedOptions = [];
                        var unselectedOptions = [];

                        select.querySelectorAll('option').forEach(function(option) {
                            if (option.selected || option.hasAttribute('selected')) {
                                selectedOptions.push(option);
                            } else {
                                unselectedOptions.push(option);
                            }
                        });

                        if (selectedOptions.length > 0) {
                            selectedOptions.forEach(function(option) {
                                select.appendChild(option);
                            });
                            unselectedOptions.forEach(function(option) {
                                select.appendChild(option);
                            });
                            selectedOptions.reverse().forEach(function(option) {
                                select.insertBefore(option, select.firstChild);
                            });
                        }

                        select.dataset.sortedInit = 'true';
                        select.scrollTop = 0;
                    }
                });
            }

            function initGlobalSelect2() {
                if (typeof $ !== 'undefined' && $.fn.select2) {
                    $('select:not(.no-select2)').each(function() {
                        var $this = $(this);
                        if (!$this.hasClass('select2-hidden-accessible')) {
                            var dropdownParent = null;
                            var $parentModal = $this.closest('.modal');
                            if ($parentModal.length > 0) {
                                dropdownParent = $parentModal;
                            }
                            $this.select2({
                                dropdownParent: dropdownParent,
                                width: '100%'
                            });
                        }
                    });
                }
            }

            // Run on AJAX complete
            if (typeof $ !== 'undefined') {
                $(document).ajaxComplete(function() {
                    sortSelectMultiple();
                    initGlobalSelect2();
                });

                // Graceful session expiry handler: Redirect directly to login on expired session without showing errors
                $(document).ajaxError(function(event, jqXHR) {
                    if (jqXHR.status === 401 || jqXHR.status === 419 || (jqXHR.responseJSON && jqXHR.responseJSON.session_expired)) {
                        window.location.href = "{{ route('login') }}";
                    }
                });
            }

            // Observer to watch for dynamic DOM additions in modal
            var observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.addedNodes.length > 0) {
                        sortSelectMultiple();
                        initGlobalSelect2();
                    }
                });
            });

            document.addEventListener("DOMContentLoaded", function() {
                var modal = document.getElementById('customModal');
                if (modal) {
                    observer.observe(modal, {
                        childList: true,
                        subtree: true
                    });
                }
                sortSelectMultiple();
                initGlobalSelect2();
            });

            document.addEventListener('shown.bs.modal', function() {
                sortSelectMultiple();
                initGlobalSelect2();
            });
        })();
    </script>
</body>
<!-- [Body] end -->

</html>
