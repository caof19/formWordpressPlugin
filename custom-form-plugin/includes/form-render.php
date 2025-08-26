<?php
$forms = get_option( 'cfp_forms', [] );
$sets  = get_option( 'cfp_topic_sets', [] );

$page_id = get_queried_object_id();
$config = $forms[ $page_id ] ?? $forms['default'] ?? [
    'title' => 'Contact Form',
    'set'   => '',
    'fields' => [
        'fio'     => [ 'show' => true, 'required' => true,  'placeholder' => '' ],
        'company' => [ 'show' => true, 'required' => false, 'placeholder' => '' ],
        'email'   => [ 'show' => true, 'required' => true,  'placeholder' => '' ],
        'phone'   => [ 'show' => true, 'required' => false, 'placeholder' => '' ],
        'comment' => [ 'show' => true, 'required' => false, 'placeholder' => '' ],
    ],
    'agreements' => [],
];

if ( isset( $_GET['cfp_sent'] ) ) {
    echo '<div class="cfp-success">Thank you! Your message was sent.</div>';
}
?>
<div class="cfp-form-wrapper">
    <h2><?php echo esc_html( $config['title'] ); ?></h2>
    <form method="post">
        <?php if ( ! empty( $config['set'] ) && isset( $sets[ $config['set'] ] ) ) : ?>
            <p>
                <label for="cfp_topic">Topic</label>
                <select name="cfp_topic" id="cfp_topic" required>
                    <option value="">-- Select --</option>
                    <?php foreach ( $sets[ $config['set'] ]['topics'] as $index => $topic ) : ?>
                        <option value="<?php echo esc_attr( $index ); ?>"><?php echo esc_html( $topic['name'] ); ?></option>
                    <?php endforeach; ?>
                </select>
            </p>
        <?php endif; ?>
        <?php if ( $config['fields']['fio']['show'] ) : ?>
            <p><input type="text" name="fio" placeholder="<?php echo esc_attr( $config['fields']['fio']['placeholder'] ); ?>" <?php echo $config['fields']['fio']['required'] ? 'required' : ''; ?> /></p>
        <?php endif; ?>
        <?php if ( $config['fields']['company']['show'] ) : ?>
            <p><input type="text" name="company" placeholder="<?php echo esc_attr( $config['fields']['company']['placeholder'] ); ?>" <?php echo $config['fields']['company']['required'] ? 'required' : ''; ?> /></p>
        <?php endif; ?>
        <?php if ( $config['fields']['email']['show'] ) : ?>
            <p><input type="email" name="email" placeholder="<?php echo esc_attr( $config['fields']['email']['placeholder'] ); ?>" <?php echo $config['fields']['email']['required'] ? 'required' : ''; ?> /></p>
        <?php endif; ?>
        <?php if ( $config['fields']['phone']['show'] ) : ?>
            <p><input type="text" name="phone" placeholder="<?php echo esc_attr( $config['fields']['phone']['placeholder'] ); ?>" <?php echo $config['fields']['phone']['required'] ? 'required' : ''; ?> /></p>
        <?php endif; ?>
        <?php if ( $config['fields']['comment']['show'] ) : ?>
            <p><textarea name="comment" placeholder="<?php echo esc_attr( $config['fields']['comment']['placeholder'] ); ?>" <?php echo $config['fields']['comment']['required'] ? 'required' : ''; ?>></textarea></p>
        <?php endif; ?>
        <?php foreach ( $config['agreements'] as $i => $agr ) : ?>
            <p class="cfp-agreement"><label><input type="checkbox" name="agreement[<?php echo $i; ?>]" <?php echo $agr['required'] ? 'required' : ''; ?> /> <?php echo $agr['text']; ?></label></p>
        <?php endforeach; ?>
        <p>
            <input type="hidden" name="cfp_form_submission" value="1" />
            <input type="hidden" name="cfp_page_id" value="<?php echo esc_attr( $page_id ); ?>" />
            <button type="submit">Send</button>
        </p>
    </form>
</div>
