<?php namespace IET_OU\Open_Media_Player;

/**
 * Open Media Player.
 *
 * oEmbed provider classes that wish to have local embeds should implement this.
 *
 * @example (YouTube):  <code>http://ouplayer/embed/-/youtube.com/EQEy5_QE2tQ</code>
 *
 * @copyright Copyright 2011-2015 The Open University (IET-OU).
 * @author N.D.Freear, 8 July 2015.
 */

interface Oembed_Local_Embed_Interface {

    public function local_embed($id);
}
