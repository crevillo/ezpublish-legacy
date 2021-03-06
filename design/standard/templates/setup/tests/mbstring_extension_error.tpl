{* DO NOT EDIT THIS FILE! Use an override template instead. *}
<h3>{$result_number}. {"Missing MBString extension"|i18n("design/standard/setup/tests")}</h3>

<p>
 {"eZ Publish comes with a good list of supported charsets by default, however they can be a bit slow due to being made in pure PHP code. Luckily eZ Publish supports the mbstring extension for handling some of the charsets."|i18n("design/standard/setup/tests")}
</p>
<p>
 {"By enabling the mbstring extension eZ Publish will have access to more charsets and also be able to process some of them faster, such as Unicode and iso-8859-*. This is recommended for multilingual sites and sites with more exotic charsets."|i18n("design/standard/setup/tests")}
</p>
<p>
 {"The complete list of charsets mbstring supports are"|i18n("design/standard/setup/tests")}:
</p>
<p class="example">
 {section name=Charset loop=$test_result[2].charset_list}
 {$:item}
 {delimiter}, {/delimiter}
 {/section}
</p>
<h3>{"Installation"|i18n("design/standard/setup/tests")}</h3>
<p>
 {"Installation of the mbstring extension is done by compiling PHP with the"|i18n("design/standard/setup/tests")} <tt class="option">--enable-mbstring</tt> {"option."|i18n("design/standard/setup/tests")}
 {"More information on enabling the extension can be found at"|i18n("design/standard/setup/tests")} <a target="_other" href="http://www.php.net/manual/en/ref.mbstring.php">php.net</a>.
</p>
<blockquote class="note">
 <p>
  <b>{"Note"|i18n("design/standard/setup/tests")}:</b>
  {"Do not enable mbstring function overloading, eZ Publish will only use the extension whenever it's needed."|i18n("design/standard/setup/tests")}
 </p>
</blockquote>
