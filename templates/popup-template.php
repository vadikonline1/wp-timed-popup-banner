<?php
/**
 * Popup template file
 * 
 * This template can be overridden by creating a file in your theme:
 * your-theme/tpb-templates/popup-template.php
 */
if (!defined('ABSPATH')) {
    exit;
}

$image_url = $args['image_url'] ?? '';
$redirect_url = $args['redirect_url'] ?? '';
$redirect_type = $args['redirect_type'] ?? 'none';

if (empty($image_url)) {
    return;
}
?>

<div id="tpb-popup-overlay" class="tpb-popup-overlay" style="display: none;">
    <div class="tpb-popup-container">
        <div class="tpb-popup-content">
            <button type="button" class="tpb-popup-close">&times;</button>
            
            <?php if ($redirect_type !== 'none' && !empty($redirect_url)): ?>
            <a href="<?php echo esc_url($redirect_url); ?>" 
               class="tpb-popup-link" 
               <?php echo $redirect_type === 'url' ? 'target="_blank"' : ''; ?>>
            <?php endif; ?>
            
                <img src="<?php echo esc_url($image_url); ?>" 
                     alt="Popup Image" 
                     class="tpb-popup-image">
            
            <?php if ($redirect_type !== 'none' && !empty($redirect_url)): ?>
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>
