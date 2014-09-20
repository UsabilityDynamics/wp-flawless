<?php
/**
 * Top Navigation bar
 *
 *
 *
 *
 *
 * @package Flawless
 * @since Flawless 0.3.3
 *
 */
 
 ?>

 <?php if( $flawless[ 'navbar' ][ 'html' ] ) { ?>

<div class="navbar navbar-fixed-top <?php echo ( $flawless[ 'mobile' ][ 'use_mobile_navbar']  == 'true' ? 'not-for-mobile' : '' ); ?>">
  <div class="navbar-inner">

    <div class="container">

      <?php if( $flawless[ 'navbar' ][ 'show_brand' ] == 'true' ) {
        echo '<a href="' . get_bloginfo( 'url') . '" class="brand">' . get_bloginfo() . '</a>';
      } ?>

      <?php if( $flawless[ 'navbar' ][ 'collapse' ] ) { ?>
      <a data-target=".nav-collapse" data-toggle="collapse" class="btn btn-navbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </a>
      <?php } ?>

      <?php echo $flawless[ 'navbar' ][ 'html' ]; ?>

    </div>

  </div>
</div>

<?php } ?>

<?php if( $flawless[ 'mobile_navbar' ][ 'html' ] ) { ?>
<div class="navbar navbar-fixed-top mobile-only">
  <div class="navbar-inner">

    <div class="container">

      <?php if( $flawless[ 'mobile_navbar' ][ 'show_brand' ] == 'true' ) {
        echo '<a href="' . get_bloginfo( 'url') . '" class="brand">' . get_bloginfo() . '</a>';
      } ?>

      <a data-target=".nav-collapse-mobile" data-toggle="collapse" class="btn btn-navbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </a>

      <?php echo $flawless[ 'mobile_navbar' ][ 'html' ]; ?>

    </div>

  </div>
</div>
<?php } ?>