<h4 class="content-subhead">About ImageMagick/GraphicsMagick support</h4>
				
<blockquote>
    <p>The ImageMagick/GraphicsMagick package and Imagick/Gmagick library are required for PDF Light Viewer plugin to work. If you've got requirement error that it's not supported, then in current context it means that the Imagick library is not installed or turned off on the server. To make plugin work you will need to install it or to turn it on.</p>
    <p><a href="http://en.wikipedia.org/wiki/ImageMagick" target="_blank">ImageMagick</a> is the image manipulation library for PHP. ImageMagick is a server software and currently not included into the plugin.</p>
    <p>To make it work, someone, you, your server administrator or your hosting provider, should install (or enable, if it's already installed) this software on your server. If your site is maintained by some administrator, he or she already knows how to install ImageMagick and Imagick libraries. Otherwise the installation could be done through cPanel in some cases and you could try to install it.</p>
    <p>Sometimes your hosting provider could restrict installation of additional software. In this case you can contact them for further instructions and details.</p>
    <p>To install ImageMagick manually please visit <a href="http://php.net/manual/en/imagick.setup.php" target="_blank">this installation guide</a>.</p>
</blockquote>

<h4 class="content-subhead">About GhostScript support</h4>

<blockquote cite="Noticed by Alexander">
    <p>The GhostScript package is required for PDF Light Viewer plugin to work. To make plugin work you will need to install it on the server.</p>
    <p><a href="https://en.wikipedia.org/wiki/Ghostscript" target="_blank">GhostScript</a> is the application to work with PDF files. GhostScript is a server software and currently not included into the plugin.</p>
    <p>To make it work, someone, you, your server administrator or your hosting provider, should install this software on your server. If your site is maintained by some administrator, he or she already knows how to install GhostScript.</p>
    <p>To install GhostScript manually please visit <a href="http://ghostscript.com/doc/current/Install.htm" target="_blank">this installation guide</a>.</p>
    <p>
        <b>Big thanks to <i>Alexander</i> for this notice.</b>
    </p>
    <p><b>GhostScript is required for Imagick PDF Support. For cases, when you are sure that GhostScript is installed, but it was not detected by the plugin correctly you can disable this requirement in plugin's settings.</b></p>
</blockquote>
