<div class="row">
    <div class="col-md-2">
    
        <h3>Featured Image</h3>
        <p class="help-block">Some helpful text</p>
                
    </div>
    <!-- /.col-md-2 -->
                
    <div class="col-md-10">

        <div class="form-group">
            <?php echo \Assets\Admin\Controllers\Assets::instance()->fetchElementImage('featured_image', $flash->old('details.featured_image.slug'), array('field'=>'details[featured_image][slug]') ); ?>
        </div>
        <!-- /.form-group -->
    
    </div>
    <!-- /.col-md-10 -->
</div>
<!-- /.row -->