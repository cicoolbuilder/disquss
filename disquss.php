<?php 
namespace Disquss;

app()->load->library('cc_html');


app()->cc_html->registerScriptFileBottom("
    
    $(function(){
        $('.switch-button-disqus').switchButton({
            labels_placement: 'right'
        });
    });

 ", 'script');

app()->cc_html->registerCssFile(BASE_ASSET . "/jquery-switch-button/jquery.switchButton.css");

cicool()->addTabSetting([
    'id' => 'disqus',
    'label' => 'Discuss',
    'icon' => 'fa fa-commenting-o',
])->addTabContent([
    'content' => ' 

    <div class="col-md-12">
        <div class="col-sm-12">
            <label>Enable Discuss</label><br>
            <input type="checkbox" class="switch-button-disqus" name="enable_disqus" '.(get_option('enable_disqus') == 1 ? 'checked' : '').' id="enable_disqus" value="1">
            <small class="info help-block">Enable Discuss Comment.</small>
        </div>

    </div>
    <div class="clear"></div>
    <div class="col-md-6">


      <div class="col-sm-12">
          <label>Discuss ID</label>
          <input type="text" class="form-control" name="disqus_id" id="disqus_id" value="'.get_option('disqus_id').'">
          <small class="info help-block">The id of disqus
          <br>
          You can get this on '.anchor('https://disqus.com/admin/create/', '', ['target' => 'blank']).' .</small>
      </div>

    </div>
    '
])
->settingBeforeSave(function($form){
})
->settingOnSave(function($ci){
    set_option('enable_disqus', $ci->input->post('enable_disqus'));
    set_option('disqus_id', $ci->input->post('disqus_id'));
});

if (get_option('enable_disqus')) {
    app()->cc_app->onEvent('blog_read_bottom', function($blog) {
        $disqus_id = get_option('disqus_id');
         if ($disqus_id== null) {
             echo '
                <hr>
                <div class="alert alert-warning">
                <i class="fa fa-commenting-o"></i>
                Your discuss account not set on configuration, please fill Disqus ID on <br><br>'.anchor('administrator/setting/?tab=tab_disqus', '<i class="fa fa-cog"></i> configuration', ['class' => 'btn btn-xs btn-info btn-flat']).'</div>
                ';
         } else {
             echo '
                <hr>
                <h2>Comment</h2>
                <div id="disqus_thread"></div>
                ';

                $http = '';
                $fo = str_replace("index.php","", $_SERVER['SCRIPT_NAME']);
                $base = $base = "$http" . $_SERVER['SERVER_NAME'] . $fo; 
                ?>
                <script>


                  var disqus_config = function () {
                      this.page.url = "http://<?= $base.'/blog/'.$blog->id; ?>";
                      this.page.identifier = "<?= $blog->id; ?>";
                      this.page.title = '<?= $blog->title ?>';
                  };
                  
                  (function() {  // REQUIRED CONFIGURATION VARIABLE: EDIT THE SHORTNAME BELOW
                      var d = document, s = d.createElement('script');
                      s.src = '//<?= $disqus_id ?>.disqus.com/embed.js';  // IMPORTANT: Replace EXAMPLE with your forum shortname!
                      
                      s.setAttribute('data-timestamp', +new Date());
                      (d.head || d.body).appendChild(s);
                  })();
                   
                </script>
                <noscript>
                    Please enable JavaScript to view the 
                    <a href="https://disqus.com/?ref_noscript" rel="nofollow">
                        comments powered by Disqus.
                    </a>
                </noscript>
            <?php
         }
    });
}


define('DISQUSS_EXT', basename(__DIR__));

if ($ccExtension->actived()) {
   app()->cc_app->onEvent('extension_info_'.DISQUSS_EXT, function(){
    echo '<div class="callout callout-warning-cc ">go to page '.anchor('administrator/setting/?tab=tab_disqus', 'setting', ['class' => 'btn btn-xs btn-info btn-flat']).' for configuration</div>';
    });
}

