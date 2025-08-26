<?php
if ( ! current_user_can( 'manage_options' ) ) {
    return;
}

$sets = get_option( 'cfp_topic_sets', [] );
$forms = get_option( 'cfp_forms', [] );
$page_id = isset( $_GET['cfp_form_page'] ) ? sanitize_text_field( $_GET['cfp_form_page'] ) : 'default';

if ( isset( $_POST['cfp_save_form'] ) ) {
    $page_id = sanitize_text_field( $_POST['cfp_form_page'] );
    $form = [
        'title'       => sanitize_text_field( $_POST['title'] ?? '' ),
        'set'         => sanitize_text_field( $_POST['set'] ?? '' ),
        'fields'      => [],
        'agreements'  => [],
    ];
    $field_names = [ 'fio', 'company', 'email', 'phone', 'comment' ];
    foreach ( $field_names as $f ) {
        $form['fields'][$f] = [
            'show'       => isset( $_POST['field'][$f]['show'] ),
            'required'   => isset( $_POST['field'][$f]['required'] ),
            'placeholder'=> sanitize_text_field( $_POST['field'][$f]['placeholder'] ?? '' ),
        ];
    }
    if ( ! empty( $_POST['agreements'] ) ) {
        foreach ( $_POST['agreements'] as $agr ) {
            $form['agreements'][] = [
                'text'     => wp_kses_post( $agr['text'] ?? '' ),
                'required' => ! empty( $agr['required'] ),
            ];
        }
    }
    $forms[ $page_id ] = $form;
    update_option( 'cfp_forms', $forms );
    echo '<div class="updated"><p>Saved.</p></div>';
}

$current = $forms[ $page_id ] ?? [
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
?>
<div class="wrap">
    <h1>Form Settings</h1>
    <form method="get" id="cfp-page-selector">
        <input type="hidden" name="page" value="cfp_form_settings" />
        <label for="cfp_form_page">Select Page:</label>
        <select name="cfp_form_page" id="cfp_form_page" onchange="this.form.submit();">
            <option value="default" <?php selected( $page_id, 'default' ); ?>>Standard</option>
            <?php
            $pages = get_pages( [ 'post_type' => 'any', 'post_status' => 'any', 'number' => 0 ] );
            foreach ( $pages as $p ) {
                echo '<option value="' . esc_attr( $p->ID ) . '"' . selected( $page_id, $p->ID, false ) . '>' . esc_html( $p->post_title ) . '</option>';
            }
            ?>
        </select>
    </form>

    <form method="post" id="cfp-form-settings">
        <input type="hidden" name="cfp_form_page" value="<?php echo esc_attr( $page_id ); ?>" />
        <table class="form-table">
            <tr>
                <th scope="row"><label for="title">Form Title</label></th>
                <td><input type="text" name="title" id="title" value="<?php echo esc_attr( $current['title'] ); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th scope="row">Topic Set</th>
                <td>
                    <select name="set">
                        <option value="">-- Select Set --</option>
                        <?php foreach ( $sets as $index => $set ) : ?>
                            <option value="<?php echo $index; ?>" <?php selected( $current['set'], (string) $index ); ?>><?php echo esc_html( $set['name'] ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </table>
        <h2>Fields</h2>
        <table class="widefat fixed">
            <thead>
                <tr><th>Field</th><th>Show</th><th>Required</th><th>Placeholder</th></tr>
            </thead>
            <tbody>
                <?php foreach ( $current['fields'] as $key => $field ) : ?>
                    <tr>
                        <td><?php echo esc_html( ucfirst( $key ) ); ?></td>
                        <td><input type="checkbox" name="field[<?php echo $key; ?>][show]" <?php checked( $field['show'] ); ?> /></td>
                        <td><input type="checkbox" name="field[<?php echo $key; ?>][required]" <?php checked( $field['required'] ); ?> /></td>
                        <td><input type="text" name="field[<?php echo $key; ?>][placeholder]" value="<?php echo esc_attr( $field['placeholder'] ); ?>" /></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <h2>Agreements</h2>
        <div id="cfp-agreements">
            <?php foreach ( $current['agreements'] as $i => $agr ) : ?>
                <div class="cfp-agreement">
                    <input type="text" name="agreements[<?php echo $i; ?>][text]" value="<?php echo esc_attr( $agr['text'] ); ?>" placeholder="Agreement text" class="regular-text" />
                    <label><input type="checkbox" name="agreements[<?php echo $i; ?>][required]" <?php checked( $agr['required'] ); ?> /> Required</label>
                </div>
            <?php endforeach; ?>
        </div>
        <button class="button" id="cfp-add-agreement">Add Agreement</button>
        <?php submit_button( 'Save Form', 'primary', 'cfp_save_form' ); ?>
    </form>
</div>
