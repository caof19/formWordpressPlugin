<?php
if ( ! current_user_can( 'manage_options' ) ) {
    return;
}

$sets = get_option( 'cfp_topic_sets', [] );

if ( isset( $_POST['cfp_save_sets'] ) ) {
    $raw_sets = $_POST['sets'] ?? [];
    $clean_sets = [];
    foreach ( $raw_sets as $set ) {
        $set_name = sanitize_text_field( $set['name'] ?? '' );
        if ( ! $set_name ) {
            continue;
        }
        $topics = [];
        if ( ! empty( $set['topics'] ) ) {
            foreach ( $set['topics'] as $topic ) {
                $topic_name = sanitize_text_field( $topic['name'] ?? '' );
                if ( ! $topic_name ) {
                    continue;
                }
                $emails = array_filter( array_map( 'sanitize_email', $topic['emails'] ?? [] ) );
                $topics[] = [
                    'name'   => $topic_name,
                    'emails' => $emails,
                ];
            }
        }
        $clean_sets[] = [
            'name'   => $set_name,
            'topics' => $topics,
        ];
    }
    update_option( 'cfp_topic_sets', $clean_sets );
    $sets = $clean_sets;
    echo '<div class="updated"><p>Saved.</p></div>';
}
?>
<div class="wrap">
    <h1>Topic Sets</h1>
    <form method="post" id="cfp-topic-sets-form">
        <div id="cfp-topic-sets">
            <?php foreach ( $sets as $i => $set ) : ?>
                <div class="cfp-set">
                    <h2>Set</h2>
                    <input type="text" name="sets[<?php echo $i; ?>][name]" value="<?php echo esc_attr( $set['name'] ); ?>" placeholder="Set name" />
                    <div class="cfp-topics" data-name="sets[<?php echo $i; ?>][topics]">
                        <?php foreach ( $set['topics'] as $j => $topic ) : ?>
                            <div class="cfp-topic">
                                <input type="text" name="sets[<?php echo $i; ?>][topics][<?php echo $j; ?>][name]" value="<?php echo esc_attr( $topic['name'] ); ?>" placeholder="Topic name" />
                                <div class="cfp-emails" data-name="sets[<?php echo $i; ?>][topics][<?php echo $j; ?>][emails]">
                                    <?php foreach ( $topic['emails'] as $k => $email ) : ?>
                                        <input type="email" name="sets[<?php echo $i; ?>][topics][<?php echo $j; ?>][emails][<?php echo $k; ?>]" value="<?php echo esc_attr( $email ); ?>" placeholder="Email" />
                                    <?php endforeach; ?>
                                    <button class="button add-email">Add Email</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <button class="button add-topic">Add Topic</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <button id="cfp-add-set" class="button">Add Set</button>
        <?php submit_button( 'Save Sets', 'primary', 'cfp_save_sets' ); ?>
    </form>
</div>
