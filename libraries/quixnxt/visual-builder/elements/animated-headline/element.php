<!-- Blurb Element -->
<QuixTemplate id="animated-headline-template">
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
    <!-- Element Script -->
    <?php echo file_get_contents(__DIR__ . "/partials/script.twig") ?>
  </QuixScript>
</QuixTemplate>
