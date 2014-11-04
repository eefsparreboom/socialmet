<?php

class NewsArchiveWidget extends WP_Widget {
    private $textDomain = 'bootstrap-basic';
	function __construct() {
		// Instantiate the parent object
		parent::__construct(
	 		'bb_newsarchive_widget', // Base ID
			'News Archive Widget', // Name
			array( 'description' => __( 'Right side news archive'), ) // Args
		);
        
	}
    
    
	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
        extract( $args );
		echo $before_widget;
        ?>
        
        <?php echo $before_title.  $instance['title'] .$after_title;?>
        <ul>
            <?php
                $query = new WP_Query(array('post_type'=>'post'));
                if($query->have_posts()):
                    ?>
                        
                            
            <?php
                    while($query->have_posts()):$query->the_post();
                   
                ?>
                        <li>
                            <div class="cDivPostDate"><?php the_date();?></div>
                            <?php the_title(); ?>
                            &nbsp;<a href="<?php the_permalink();?>">lees meer..</a>
                        </li>
            <?php 
                    endwhile; 
                    wp_reset_postdata();
            ?>
                                
                        
            <?php
                endif;
            
             ?>		
        </ul>       
            
        <?php
        echo $after_widget;
    
        
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
			$title = ( isset( $instance[ 'title' ] ) ) ? $instance[ 'title' ] : '';
		?>
        
		<p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
            <input class="widefat" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title;?>" />
		</p>
		
		<?php 
	}
}


?>