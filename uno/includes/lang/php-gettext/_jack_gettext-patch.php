=== modified file 'gettext.php'
--- old/gettext.php	2015-11-11 16:53:37 +0000
+++ new/gettext.php	2016-08-26 22:03:16 +0000
@@ -98,7 +98,7 @@
    * @param object Reader the StreamReader object
    * @param boolean enable_cache Enable or disable caching of strings (default on)
    */
-  function gettext_reader($Reader, $enable_cache = true) {
+  function __construct($Reader, $enable_cache = true) {
     // If there isn't a StreamReader, turn on short circuit mode.
     if (! $Reader || isset($Reader->error) ) {
       $this->short_circuit = true;

=== modified file 'streams.php'
--- old/streams.php	2010-02-15 11:01:10 +0000
+++ new/streams.php	2016-08-26 22:02:58 +0000
@@ -49,7 +49,7 @@
   var $_pos;
   var $_str;
 
-  function StringReader($str='') {
+  function __construct($str='') {
     $this->_str = $str;
     $this->_pos = 0;
   }
@@ -86,7 +86,7 @@
   var $_fd;
   var $_length;
 
-  function FileReader($filename) {
+  function __construct($filename) {
     if (file_exists($filename)) {
 
       $this->_length=filesize($filename);
@@ -143,7 +143,7 @@
 // Preloads entire file in memory first, then creates a StringReader
 // over it (it assumes knowledge of StringReader internals)
 class CachedFileReader extends StringReader {
-  function CachedFileReader($filename) {
+  function __construct($filename) {
     if (file_exists($filename)) {
 
       $length=filesize($filename);

