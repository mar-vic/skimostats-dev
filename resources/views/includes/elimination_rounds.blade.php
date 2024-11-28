<div>
    <h2>HTMX</h2>
    <button hx-get="/">
        Reload the page
    </button>
    <h2 x-data="{ message: 'I ❤️ Alpine' }" x-text="message"></h2>
    <div x-data="{ count: 0 }">
        <button x-on:click="count++">Increment</button>

        <span x-text="count"></span>
    </div>
    <h2>Sprint Heats and Finals</h2>
</div>
