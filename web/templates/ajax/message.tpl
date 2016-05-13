{if !empty($messages)}
    <ul class="messages">
        {foreach $messages as $message}
            <li>{$message}</li>
        {/foreach}
    </ul>
{/if}
{if !empty($errors)}
    <ul class="errors">
        {foreach $errors as $error}
            <li>{$error}</li>
        {/foreach}
    </ul>
{/if}