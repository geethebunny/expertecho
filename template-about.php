<?php /* Template Name: About Us */

# After Header
add_action('genesis_after_header', 'page_after_header');
function page_after_header()
{
?>
    <?php $mission_section = get_field('mission_section'); ?>
    <section class='mission-section'>
        <div class='wrap cols'>
            <div class='col col--left'>
                <img src='<?php echo $mission_section['image']['url']; ?>'>
            </div>
            <div class='col col--right'>
                <h2><?php echo $mission_section['heading']; ?></h2>
                <div class='text'><?php echo $mission_section['text']; ?></div>
            </div>
        </div>
    </section>

    <?php $what_we_do_section = get_field('what_we_do_section'); ?>
    <section class='what-we-do-section'>
        <div class='wrap cols'>
            <div class='col col--left'>
                <h2><?php echo $what_we_do_section['heading']; ?></h2>
                <div class='text'><?php echo $what_we_do_section['text']; ?></div>
            </div>
            <div class='col col--right'>
                <img src='<?php echo $what_we_do_section['image']['url']; ?>'>
            </div>
        </div>
    </section>

    <?php $cta_section = get_field('cta_section'); ?>
    <section class='cta-section'>
        <div class='wrap wrap--narrow'>
            <h2><?php echo $cta_section['heading']; ?></h2>
            <div class='text'><?php echo $cta_section['text']; ?></div>
            <a href='<?php echo $cta_section['button_url']; ?>' class='button'>
                <?php echo $cta_section['button_text']; ?>
            </a>
        </div>
    </section>

    <?php $who_we_are_section = get_field('who_we_are_section'); ?>
    <section class='who-we-are-section'>
        <div class='wrap wrap--narrow'>
            <img src='<?php echo $who_we_are_section['image']['url']; ?>'>
            <h2><?php echo $who_we_are_section['heading']; ?></h2>
            <div class='text'><?php echo $who_we_are_section['text']; ?></div>
        </div>
    </section>
<?php
}

genesis();
