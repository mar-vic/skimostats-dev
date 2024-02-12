<template>
        <iframe :src="'https://www.stay22.com/embed/gm?aid=654e196519a26a9c41f1d115&address=' + location + '&checkin=' + checkin + '&checkout=' + checkout" id="stay22-widget" width="100%" height="480" frameborder="0"></iframe>
</template>
<script>
export default {
        props: {
                location: {
                        required: true,
                },
                checkin: {
                        required: true,
                },
                checkout: {
                        required: true,
                },
        }
}
</script>
