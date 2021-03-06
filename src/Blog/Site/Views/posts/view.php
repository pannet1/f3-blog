<?php
$settings = \Blog\Models\Settings::fetch(); 
$safemode_enabled = \Base::instance()->get('safemode.enabled');
$safemode_user = \Base::instance()->get('safemode.username');
$display_author = !( $safemode_enabled && ($safemode_user == $item->{'author.username'} ) );
$aside = false;
// are there tags?  // are there categories? // TODO: is a module published in the blog-post-aside position? 
if ($tags = \Blog\Models\Posts::distinctTags() || $cats = \Blog\Models\Categories::find() || $item->{'shop.products'}) {
	$aside = true;
}

$settings_admin = \Admin\Models\Settings::fetch();
$is_kissmetrics = $settings_admin->enabledIntegration( 'kissmetrics' );
?>

<div class="blog-post">
    <div class="container">
        <div class="row">
            <div class="col-sm-<?php echo !empty($aside) ? '9' : '12'; ?>">
                <article class="blog-article">
                    <h1><?php echo $item->{'title'}; ?></h1>
                    
                    <hr/>
                    
                    <p class="byline">
                        <span class="publication-date"><?php echo date( 'd F Y', $item->{'publication.start.time'} ); ?></span>                            
                        <?php if( $display_author ) { ?>                        
                        <span class="author">by <a href="./blog/author/<?php echo $item->{'author.username'}; ?>"><?php echo $item->{'author.name'}; ?></a></span>
                        <?php } ?>
                    </p>
                    
                    <div class="share-wrapper">
                        <?php echo $this->renderLayout('Blog/Site/Views::posts/social.php'); ?>
                    </div>                    
                    
                    <?php if ($item->{'featured_image.slug'}) { ?>
                    <figure>
                        <img class="img-responsive" src="./asset/<?php echo $item->{'featured_image.slug'} ?>">
                    </figure>
                    <?php } ?>
                    
                    <div class="copy-wrapper">
                        <?php echo $item->{'copy'}; ?>
                    </div>
                    
                </article>
                
                <hr/>
                
                <div class="entry-meta">
                
					<?php if(!empty( $item->{'tags'} ) ) { ?>
                        <p class="tags"> 
                            <?php foreach ( $item->{'tags'} as $tag ) { ?>
                        		<a class="label label-primary tag" href="./blog/tag/<?php echo $tag; ?>"><?php echo $tag; ?></a>
                            <?php } ?>
                        </p>
                    <?php } ?>                        
                    
                    <?php if (!empty($item->{'categories'})) { ?>
                    <p class="categories"> 
                        <?php foreach ($item->{'categories'} as $category) { ?>
                        <a class="label label-info category" href="./blog/category/<?php echo $category['slug']; ?>"
                            title="View all posts in <?php echo $category['title']; ?>" rel="category tag"><?php echo $category['title']; ?></a>
                        <?php } ?>
                    </p>
                    <?php } ?>
    
                </div>
                
                <?php if (!empty( $author ) && $display_author ) { ?>
                    <?php $img = $author->profilePicture(); ?>
                    <?php if ($img || $author->{'blog.bio.short'}) { ?>
                    <div class="author-box">
                        <?php if ($img) { ?>
                        <figure>
                        	<a href="./blog/author/<?php echo $author->{'username'}; ?>">
    							<img src="<?php echo $img; ?>" alt="<?php echo $item->{'author.name'}; ?>" class="img-rounded">
    						</a>
                        </figure>
                        <?php } ?>
                        
                        <?php if ($author->{'blog.bio.short'}) { ?>
                        <div class="text">
                            <h4>About the author</h4>
                            <div>
                            	<?php echo $author->{'blog.bio.short'}; ?>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                    <?php } ?>
                <?php }?>
                
                <?php if( !empty( $related ) ) { ?>
                <div class="related-posts main-widget">
                
                    <div class="widget-title">
                        <h4>Related Posts</h4>

                        <div class="slider-controls related-post-controls">
                            <button class="prev"><i class="glyphicon glyphicon-chevron-left"></i></button>
                            <button class="next"><i class="glyphicon glyphicon-chevron-right"></i></button>
                        </div>

                    </div>
                    <div class="widget-content">
                        <div class="flexslider related-posts-slider">
                            <ul class="slides">
                                <li>
                                    <div class="row">
                                <?php
                                	$i = 0;
                                	foreach( $related as $post ) {
                                		if( $i  % 3 == 0 && $i ){ ?>
                                			</div>
                                		</li>		
                                        <li>
                                        	<div class="row">
                                        <?php } ?>
                                			<div class="col-sm-4">
                                				<figure>
                                					<a href="./blog/post/<?php echo $post->{'slug'}; ?>">
                                						<img src="./asset/thumb/<?php echo $post->{'featured_image.slug'}; ?>" alt="<?php echo $post->{'title'}; ?>"/>
                                					</a>
                                				</figure>
                                				<h5><a href="./blog/post/<?php echo $post->{'slug'}; ?>"><?php echo $post->{'title'}; ?></a></h5>
                                			</div>
                                	<?php
                                	$i++;
                                	}
                                		if( $i %3 != 0 ) { ?>
                                		</div>
                                	</li>
                                <?php	} ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <?php } ?>
                
                <?php
                if ( $type = $settings->get( "general.comments" ) ) 
                {
                    ?><hr /><?php
                    // display the comments
                    $this->item = $item;
                    echo $this->renderView('Blog/Site/Views::posts/comments.php');
                }
                ?>
            </div>
            
            <?php if (!empty($aside)) { ?>
            <aside class="col-sm-3">
            	<?php 
            		// display the categories
            		echo $this->renderView('Blog/Site/Views::categories/widget.php');
            		
            		// display the tag cloud
            		echo $this->renderView('Blog/Site/Views::tags/cloud.php');
            	?>
            	
                <?php if ($related_products = (array) $item->{'shop.products'}) { ?>
                    <div class="widget widget-related-products widget-related-products-blog">
                    <h4 class="widget-title">Related Products</h4>
                    <div class="widget-content">
                    <?php
                    	$n=0; $count = count($related_products);
                    	foreach ($related_products as $product_id) {
                        	$product = (new \Shop\Models\Products)->setState('filter.id', $product_id)->getItem();
                        	$image = (!empty($product->{'featured_image.slug'})) ? './asset/thumb/' . $product->{'featured_image.slug'} : null;
                        	$url = './shop/product/' . $product->slug;
                        	$js = '';
                        	if( $is_kissmetrics ){
								$js ="\" onclick=\"javascript:_kmq.push(['record', 'Blog Related Items', {'Product Name' : '".$product->title."', 'SKU' : '".$product->{'tracking.sku'}."', 'Blog Post' : '".$item->title."' }])";
								
								$url .= $js;
								$image .= $js;
							}
							if (empty($url) || !$product->isAvailable()) { continue; } ?>
                        
                        <div class="row related-product-blog">
                            
                            <div class="col-xs-5">
                                <?php if ($image) { ?>
                                <a href="<?php echo $url; ?>">
                                    <img class="img-responsive" src="<?php echo $image ?>">
                                </a>
                                <?php } ?>
                            </div>
                            <div class="col-xs-7">
                            	<div class="title-line">
                                <a href="<?php echo $url; ?>">
                                    <b><?php echo $product->title; ?></b>
                                </a>
                            	</div>
                                <div class="price-line">
                                    <?php if (((int) $product->get('prices.list') > 0) && $product->get('prices.list') != $product->price() ) { ?>
                                        <span class="list-price price"><strike><?php echo \Shop\Models\Currency::format( $product->{'prices.list'} ); ?></strike></span>
                                    <?php } ?>
                                    <div class="price">
                                        <?php echo \Shop\Models\Currency::format( $product->price() ); ?>
                                    </div>
            
                                </div>                        
                            </div>
                            
                        </div>
                        
                        <hr/>

                    <?php } ?>
                    </div>
                    </div>
                <?php } ?>
                            	
            </aside>
            <?php } ?>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function(){
	jQuery('.copy-wrapper').find('img').each(function(){
		var img = jQuery(this);
		if (!img.hasClass('img-responsive')) {
			img.addClass('img-responsive');
	    }
	});
});
</script>