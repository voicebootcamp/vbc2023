<QuixTemplate id="column-template">
  <QuixHtml>
    <?php echo file_get_contents(__DIR__ . "/partials/html.twig") ?>
  </QuixHtml>
  <QuixStyle>
    <!-- Element Style -->
    <?php echo file_get_contents(__DIR__ . "/partials/style.twig") ?>
  </QuixStyle>
</QuixTemplate> 