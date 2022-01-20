<?php
$em_uid = uniqid();
$className = 'em_cards_block';
if(!empty($block['className'])){
    $className .= ' '.$block['className'];
}

if(!empty($block['align'])){
    $className .= ' align'.$block['align'];
}

$em_cards = get_field('em_cards');
$dots = get_field('show_dots');
?>
<div class="<?php echo esc_attr($className);?>">
  <div class='prev em_card_button'></div>
  <div class='next em_card_button'></div>
  <div class='em_card_slide_show_wrap'>
  <?php if(!empty($em_cards)):?>
    <?php $slide_count = 0;?>
    <?php foreach($em_cards as $em_card):
        $thumbnail = get_the_post_thumbnail_url($em_card['em_card']->ID);
    ?>
        
        <div class='em_card_slide <?php echo($slide_count == 0)?'current_slide':'';?>' id='em_slide_<?php echo $em_uid;?>_<?php echo $slide_count;?>'>
            <div class='em_card_slide_content'>
                <a class='slide_permalink' href='<?php echo get_the_permalink($em_card['em_card']->ID)?>' target='_blank'></a>
                <div class='thumbnail_container <?php echo (!$thumbnail)?'no_thumbnail':'';?>'>
                    <?php if($thumbnail): ?>
                        <img src='<?php echo esc_url($thumbnail);?>'>
                    <?php endif;?>
                </div>
                <h2><?php echo htmlspecialchars($em_card['em_card']->post_title);?></h2>
                <?php if($em_card['em_card']->post_excerpt): ?>
                    <p>
                        <?php echo $em_card['em_card']->post_excerpt;?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
        
        <?php $slide_count += 1;?>
    <?php endforeach;?>
  <?php endif;?>
  </div>
  <?php if(!empty($em_cards) && $dots):?>
  <ul class='em_card_dots'>
    <?php $slide_count = 0;?>
    <?php foreach($em_cards as $em_card): ?>
        <li id='em_slide_<?php echo $em_uid;?>_<?php echo $slide_count;?>_dot' data-for='#em_slide_<?php echo $em_uid;?>_<?php echo $slide_count;?>' class='<?php echo ($slide_count == 0)?'selected':'';?>'>
        </li>
        <?php $slide_count+=1;?>
    <?php endforeach;?>
  </ul>
  <?php endif;?>
</div>