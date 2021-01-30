<?php

/**
 * apply the nl2br and htmlentities function on content to secure content from outside 
 *
 * @param string $content content a priori coming from outside (form, database, ...) 
 *
 * @return void
 */
function formatHtml(string $content) {
    return nl2br(htmlentities($content));
}