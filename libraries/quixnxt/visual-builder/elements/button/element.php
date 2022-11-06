<!-- Button Element -->
<QuixTemplate id="button-template">
  <QuixHtml>
    <?php echo file_get_contents(__DIR__ . "/partials/html.twig") ?>
  </QuixHtml>
  <QuixStyle>
    <!-- Global Style -->
    <?php echo file_get_contents(__DIR__ . "/../../shared/global.twig") ?>
    <!-- Element Style -->
    <?php echo file_get_contents(__DIR__ . "/partials/style.twig") ?>
  </QuixStyle>
</QuixTemplate>
