<?php

/**
 * apply the nl2br and htmlentities function on content to secure content from outside 
 *
 * @param String $content content a priori coming from outside (form, database, ...) 
 *
 * @return void
 */
function formatHtml (String $content) {
    nl2br(htmlentities($content));
}