<?php
/**
 * Title: Request information form (mock)
 * Slug: ucf-wordpress-block-theme/form
 * Categories: ucf
 * Description: A mock of the request-for-information form, styled to the design system: labels above, help before the field, the word (required), errors that name the field. It does not submit anywhere — the real form comes from the forms plugin.
 * Keywords: form, rfi, request information, contact
 *
 * @package ucf-wordpress-block-theme
 */

?>
<!-- wp:html -->
<form class="ucf-form" action="#" method="post" novalidate>
<p class="ucf-sample">Mock form for review. It does not submit; the real form comes from the forms plugin.</p>
<div class="ucf-field"><label for="rfi-name">Full name <span class="ucf-field__req">(required)</span></label><input id="rfi-name" name="name" type="text" autocomplete="name" required></div>
<div class="ucf-field is-invalid"><label for="rfi-email">Email <span class="ucf-field__req">(required)</span></label><p class="ucf-field__help" id="rfi-email-help">We send program details here, nothing else.</p><input id="rfi-email" name="email" type="email" autocomplete="email" aria-describedby="rfi-email-help rfi-email-error" aria-invalid="true" required><p class="ucf-field__error" id="rfi-email-error">Email needs an @ — for example, name@example.com.</p></div>
<div class="ucf-field"><label for="rfi-program">Program of interest</label><select id="rfi-program" name="program"><option>Choose a program</option><option>Computer Science B.S.</option><option>Psychology B.S.</option></select></div>
<fieldset><legend>When would you start?</legend><label class="ucf-choice"><input type="radio" name="term" value="fall"> Fall</label><label class="ucf-choice"><input type="radio" name="term" value="spring"> Spring</label></fieldset>
<div class="wp-block-button"><button type="submit" class="wp-block-button__link wp-element-button">Request information</button></div>
</form>
<!-- /wp:html -->
