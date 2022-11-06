<!-- Image Element -->
<QuixTemplate id="video-template">
  <QuixHtml>
    <?php echo file_get_contents(__DIR__ . "/partials/html.twig") ?>
  </QuixHtml>
  <QuixStyle>
    <!-- Global Style -->
    <?php echo file_get_contents(__DIR__ . "/../../shared/global.twig") ?>
    <!-- Element Style -->
    <?php echo file_get_contents(__DIR__ . "/partials/style.twig") ?>
  </QuixStyle>
  <QuixScript>
    <?php echo file_get_contents(__DIR__ . "/partials/script.twig") ?>
  </QuixScript>
</QuixTemplate>
