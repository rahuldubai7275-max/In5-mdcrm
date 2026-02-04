    {{-- Vendor Scripts --}}
        <script src="{{ asset(mix('vendors/js/vendors.min.js')) }}"></script>
        <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
        <script src="{{ asset(mix('vendors/js/ui/prism.min.js')) }}"></script>
{{--        <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.js')) }}"></script>--}}
{{--        <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.date.js')) }}"></script>--}}
{{--        <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.time.js')) }}"></script>--}}
{{--        <script src="{{ asset(mix('vendors/js/pickers/pickadate/legacy.js')) }}"></script>--}}
        <script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>

        @yield('vendor-script')
        <script src="{{ asset(mix('vendors/js/forms/validation/jqBootstrapValidation.js')) }}"></script>
        {{-- Theme Scripts --}}
        <script src="{{ asset(mix('js/core/app-menu.js')) }}"></script>
        <script src="{{ asset(mix('js/core/app.js')) }}"></script>
        <script src="{{ asset(mix('js/scripts/components.js')) }}"></script>

        <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
        <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>

    <!-- Datepicker -->
    <script src="/js/scripts/datepiker/persian-date.min.js"></script>
    <script src="/js/scripts/datepiker/persian-datepicker.js"></script>
    <!-- datepicker -->
@if($configData['blankPage'] == false)
		<script src="{{ asset(mix('js/scripts/customizer.js')) }}"></script>
        <script src="{{ asset(mix('js/scripts/footer.js')) }}{{'?v='.date('YmdHis')}}"></script>
@endif
{{--        <script src="{{ asset(mix('js/scripts/pickers/dateTime/pick-a-datetime.js')) }}"></script>--}}
        {{-- page script --}}
        <script src="{{ asset(mix('js/scripts/forms/validation/form-validation.js')) }}"></script>
        @yield('page-script')
