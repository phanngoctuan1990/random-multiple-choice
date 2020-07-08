<template>
    <div class="container">
        <div class="row mt-4">
            <div class="col-md">
                <router-view></router-view>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "App"
};
</script>

<style>
.container {
    padding: 1rem;
    min-height: 100%;
    background-color: #edeae5;
}
.footer {
    left: 0;
    bottom: 0;
    width: 100%;
    position: fixed;
    text-align: center;
}
</style>
