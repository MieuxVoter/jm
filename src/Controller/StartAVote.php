<?php
/**
 * Project : jm
 * File : StartAVote.php
 */

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class StartAVote
{
    public function form(){
        return new Response(
            '<html><body>FORM</body></html>'
        );
    }
}