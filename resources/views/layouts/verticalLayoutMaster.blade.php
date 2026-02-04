@php
$configData = Helper::applClasses();
@endphp
{{--vertical-layout 2-columns navbar-floating footer-static pace-done menu-expanded vertical-menu-modern semi-dark-layout--}}
<body onload="myFunction()"
  class="vertical-layout vertical-menu-modern 2-columns {{ $configData['blankPageClass'] }} {{ $configData['bodyClass'] }} {{($configData['theme'] === 'light') ? '' : $configData['layoutTheme'] }} {{ $configData['verticalMenuNavbarType'] }} {{ $configData['sidebarClass'] }} {{ $configData['footerType'] }} "
  data-menu="vertical-menu-modern" data-col="2-columns">
<div id="loader" style="position: fixed;
    bottom: 0;
    left: 0;
    top: 0;
    right: 0;
    background: {{($configData['theme']=='dark') ? '#262c49' : '#f3f3f3'}};
    z-index: 30;"></div>
  {{-- Include Sidebar --}}
  @include('panels.sidebar')

  <!-- BEGIN: Content style="display:none;"-->
  <div id="app-content" class="app-content content">
    <!-- BEGIN: Header-->
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    {{-- Include Navbar --}}
    @include('panels.navbar')

    @if(($configData['contentLayout']!=='default') && isset($configData['contentLayout']))
    <div class="content-area-wrapper">
      <div class="{{ $configData['sidebarPositionClass'] }}">
        <div class="sidebar">
          {{-- Include Sidebar Content --}}
          @yield('content-sidebar')
        </div>
      </div>
      <div class="{{ $configData['contentsidebarClass'] }}">
        <div class="content-wrapper">
          <div class="content-body">
            {{-- Include Page Content --}}
            @yield('content')
          </div>
        </div>
      </div>
    </div>
    @else
    <div class="content-wrapper pt-1">
      {{-- Include Breadcrumb --}}
      @if($configData['pageHeader'] === true && isset($configData['pageHeader']))
      @include('panels.breadcrumb')
      @endif

      <div class="content-body">
        {{-- Include Page Content --}}
        @yield('content')
      </div>
    </div>
    @endif

  </div>
  <!-- End: Content-->
	@include('admin/customizer')
  <div class="sidenav-overlay"></div>
  <div class="drag-target"></div>

  {{-- include footer --}}
  @include('panels/footer')

  {{-- include default scripts --}}
  @include('panels/scripts')

  @php
  $admin = Auth::guard('admin')->user();
  $now = date('Y-m-d H:i:s');
  $noteNotFeedback=DB::select("SELECT 'lead' as type,lead_notes.id, admin_id, property_id, note_subject, lead_id, null as 'contact_id', note, date_at, time_at, lead_notes.created_at,firstname,lastname FROM lead_notes,admins WHERE lead_notes.admin_id=admins.id AND CONCAT(`date_at`,' ',`time_at`) < '".$now."' AND note_subject IN (2,3) AND lead_notes.status=1 AND `note` is null AND admin_id=".$admin->id."
                UNION
                SELECT 'contact' as type,contact_note.id, admin_id, property_id, note_subject, null as 'lead_id', contact_id, note, date_at, time_at, contact_note.created_at,firstname,lastname FROM contact_note,admins WHERE contact_note.admin_id=admins.id AND CONCAT(`date_at`,' ',`time_at`) < '".$now."' AND note_subject IN (2,3) AND contact_note.status=1 AND `note` is null AND admin_id=".$admin->id."
                UNION
                SELECT 'property' as type,property_note.id, admin_id, property_id, note_subject, null as 'lead_id', contact_id, note, date_at, time_at, property_note.created_at,firstname,lastname FROM property_note,admins WHERE property_note.admin_id=admins.id AND CONCAT(`date_at`,' ',`time_at`) < '".$now."' AND note_subject IN (2,3) AND property_note.status=1 AND `note` is null AND admin_id=".$admin->id." ORDER BY created_at desc");
  $warnings=\App\Models\AdminWarning::where('status','0')->where('admin_id',$admin->id)->get();
  @endphp
  <script>
  <?php if(!request('fb')){ ?>
      @if($noteNotFeedback)
          $('#RegisterFeedbackModal').modal('show');
        //   warningFeedback();
      @endif
  <?php } ?>

      @if($warnings)
      $('#AcknowledgebackModal').modal('show');
      @endif

  $('body').on('click','.go-to-reg-feedback',function(){
      let type=$(this).data('type');
      let id=$(this).data('id');
      if(type=='lead'){
          window.open('/admin/lead/view/'+id+'?fb=true');
      }else if(type=='contact'){
          window.open('/admin/contact/view/'+id+'?fb=true');
      }else{
          window.open('/admin/property/view/'+id+'?fb=true');
      }
  });

  $(".checkAll").click(function(){
        $('table tbody  input:checkbox').not(this).prop('checked', this.checked);
    });

    $(".table").on('click', 'input[type="checkbox"]', function(){
        var checkboxes = $('.table tbody input[type="checkbox"]').filter(":checked").length;
        var checked=[];
        if (checkboxes != 0) {
            $('.assign-to-list').removeClass('d-none');
            $('.btn-sand-mail').removeClass('disabled');
            $('.btn-match-master-project').removeClass('disabled');
            $('.btn-match-project').removeClass('disabled');
            $(".table tbody input:checkbox[name='contact[]']:checked").each(function(){
                checked.push($(this).val());
            });
        } else {
            $('.assign-to-list').addClass('d-none');
            $('.btn-sand-mail').addClass('disabled');
            $('.btn-match-master-project').addClass('disabled');
            $('.btn-match-project').addClass('disabled');
        }

        $('#email_submit').val(checked.join());
    });

    $("body").on('click', '.btn-activity', function(){
        $('#NoteSubject').val('').change();
        $('#DateAt , #TimeAt , #Note').val('');
    });

    $("body").on('click', '.warning-acknowledge', function(){
        $('#WarningAcknowledge_id').val($(this).val());
    });
  </script>

  <script>
      var loading;

      function myFunction() {
          loading = setTimeout(showPage, 200);
      }

      function showPage() {
          document.getElementById("loader").style.display = "none";
          // document.getElementById("app-content").style.display = "block";
      }

      // $(window).keydown(function(event){
      //     if(event.keyCode == 13) {
      //         event.preventDefault();
      //         return false;
      //     }
      // });

      $(document).on("keydown", "form input", function(event) {
          // alert($(this).val());
          return event.key != "Enter";
      });

      $('form').submit(function(){
          $(this).find(':input[type=submit]').prop('disabled', true);
      });

      function toast_($title,$msg,$timeOut=20000,$closeButton=true) {
          toastr.error($msg, $title, {"closeButton": $closeButton, "timeOut": $timeOut});
      }
  </script>
  <script>
      $('.mobile-number-paste').keyup(function(){
          let val=$(this).val();
          val = val.replace(/\s/g, '');
          $(this).val(val);
      });

      $(".mobile-number-paste").bind("paste", function(e){
          var ctl = $(this);
          setTimeout(function() {
              let val=ctl.val();
              val = val.replace(/\s/g, '');
              ctl.val(val);
          }, 100);
      } );
  </script>
  <script>
      $('.format-picker').persianDatepicker({
          initialValue: false,
          format: 'YYYY-MM-DD',
          // altFormat: 'YYYY-MM-DD',
          calendarType: 'gregorian',
          gregorian:{
              locale:'en'
          },
          text:{
              btnNextText: '>'
          },
          autoClose: true,
          calendar:{
              persian: {
                  locale: 'en'
              }
          },
          toolbox:{
              enabled:true,
              todayButton:{
                  enabled: true,
              },
              calendarSwitch:{
                  enabled: false,
              },
          },
          navigator:{
              text:{
                  btnNextText:'>',
                  btnPrevText:'<'
              },
              scroll:{
                  enabled: false
              },
          },
      });
  </script>
  <script>
      $('.btn-scroll-to-top button').click(function () {
          window.scrollTo(0, 0);
      });

      window.addEventListener("scroll", (event) => {
          scrollPosition = $(window).scrollTop();
          if (scrollPosition >= 100){
              $('.btn-scroll-to-top').addClass('show');
          }else{
              $('.btn-scroll-to-top').removeClass('show');
          }
      });
  </script>
@php
    $adminAuth=Auth::guard('admin')->user();
    $Theme='';
    $MenuColor='';
    if($adminAuth) {
        $Theme = \App\Models\ThemeSetting::where('admin_id', $adminAuth->id)->first();
        if($Theme && $Theme->menu_color)
            $MenuColor = explode("|", $Theme->menu_color);
    }
@endphp
@if($Theme)
<script>
    $('#customizer-theme-colors *[data-color="{{$MenuColor[0]}}"]').addClass('selected');
    $('#customizer-navbar-colors *[data-navbar-color="{{$Theme->navbar_color}}"]').addClass('selected');
    $('.theme-layouts *[data-db-layout="{{$Theme->theme_layout}}"]').prop('checked', true);
    $('#navbar-{{$Theme->navbar_type}}').prop('checked', true);

</script>
@endif


<script>
    $(document).ready(function () {
        $('#MainNumber , #NumberTwo').on("paste", function (e) {
            e.preventDefault();
        });

        if($('.requests-menu ul a span.badge').hasClass('badge')){
            $('.blink_me').removeClass('d-none');
        }
    });

    function close_window() {
        close();
    }
</script>

</body>

</html>
