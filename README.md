<h2>Getting Started</h2>
<p>You are currently looking at the output of https://github.com/ht2/puremvc_templating/blob/master/application/index/controller/commands/view/HomeView.php.</p>
<p>This is currently set as the default view for this site, as defined in the application\index\controller\commands\application\StateCommand.php.</p>
<p>The application\index\controller\commands\application\StateCommand.php splits the current URL and passes a notification to the application\index\AppliactionFacade.php.</p>
<p>The application will then execute and run the specified command (in this case HomeView) based on the name of the notification passed.</p>

<hr>

<h2>The URL</h2>
<p>URLs are split from the root directory as such...</p>
<p>
    <code>
        <abbr title='The root of your site. If you are not running of the root of your site (a subfolder) then you may need to define this manually in the MySQL.php file.'>http://yoursiteroot.com</abbr>
        /
        <abbr title='This is main switch for views as defined in the StateCommand. This is accesible via the $this->view variable.'>view</abbr>
        /
        <abbr title='The $this->command variable can be used within a view to switch to different commans (e.g. get/edit/delete)'>command</abbr>
        /
        <abbr title='The ID is usually used to switch between different views/edits. Accessed using $this->id'>id</abbr>
    </code>
</p>
<p>With this basic setup, the StateCommand is set to route to this page and function regardless of the view/command/id.</p>
<hr>
<h2>The ExtendedSimpleCommand</h2>
<p>The <abbr title="application/common/controller/command/ExtendedSimpleCommand.php">ExtendedSimpleCommand</abbr> is the main building block of a view. It provides a number of functions to help you with templating and basic page construction and most if not all views will extend this class.</p>
<br>
<h3>BuildPage();</h3>
<p>This is by far the most useful function in this class. It utilises the <abbr title="application/common/view/Template.php">Template.php</abbr> class which listens for notifications using the <abbr title="application/common/view/TemplateMediator.php">TemplateMediator</abbr>.</p>
<pre>
//Select the template        
$this->facade->sendNotification( ApplicationFacade::TEMPLATE, $this->container );

//Apply a layout by switching the &#123;MAIN&#125; token with the defined layout template
$this->facade->sendNotification( ApplicationFacade::TOKENIZE, array( '&#123;MAIN&#125;' => $this->layout ) );

//Add pre-universal tokens
foreach($this->pre_tokens as $pre_t)
    if(is_array($pre_t)) 
        $this->facade->sendNotification( ApplicationFacade::TOKENIZE, $pre_t );

//Add universal tokens
$this->facade->sendNotification( ApplicationFacade::TOKENIZE, $this->getUniversalTokens() );

//Add post-universal tokens
foreach($this->post_tokens as $post_t)
    if( is_array($post_t) ) 
        $this->facade->sendNotification( ApplicationFacade::TOKENIZE, $post_t );

//Render the page
$this->facade->sendNotification( ApplicationFacade::RENDER );
exit();
</pre>
<br>
<h3>Tokenization</h3>
<p>The system uses a simple str_replace function to swap out tokens as defined in a set of arrays.</p>
<p>Within each array, the token is defined as the <code>key</code> and the data as the <code>value</code> e.g:</p>
<pre>
$tokens = array(
    '&#123;TOKEN1&#125;'   =>  'Hello &#123;TOKEN2&#125;,
    '&#123;TOKEN2&#125;'   =>  'world'
);
</pre>
<p>In the example above, <code>&#123;TOKEN1&#125;</code> will be swapped out first followed by <code>&#123;TOKEN2&#125;</code>, resulting in the phrase '<em>Hello world</em>' being printed for <code>&#123;TOKEN1&#125;</code>.</p>

<p>There are 3 different arrays used, meaning there is control over the order of tokenization:</p>
<ol>
    <li><code>$this->preTokens</code> (array)</li>
    <li><code>$this->universalTokens()</code> (function): This function returns an array filled with constant tokens for each page, such as &#123;HEADER&#125;, &#123;CONTENT&#125; and &#123;FOOTER&#125;.</li>
    <li><code>$this->postTokens</code> (array)</li>
</ol>
<br>
