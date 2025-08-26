<?php
if ( ! current_user_can( 'manage_options' ) ) {
    return;
}
$messages = get_option( 'cfp_messages', [] );
?>
<div class="wrap">
    <h1>Messages</h1>
    <?php if ( empty( $messages ) ) : ?>
        <p>No messages yet.</p>
    <?php else : ?>
        <table class="widefat">
            <thead><tr><th>Date</th><th>Data</th></tr></thead>
            <tbody>
                <?php foreach ( array_reverse( $messages ) as $m ) : ?>
                    <tr>
                        <td><?php echo esc_html( $m['time'] ); ?></td>
                        <td><pre><?php print_r( $m['data'] ); ?></pre></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
