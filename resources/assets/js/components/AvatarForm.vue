<template>
<div>
    <h1 v-text="user.name">
    </h1>

    <form v-if="canUpdate" method="POST" enctype="multipart/form-data">
    <!--action omitted to perform w/ JavaScript-->
    <!--{{ csrf_field() }}-->
    <!--csrf is omitted and done is bootstrap.js-->
        <input type="file" name="avatar" accept="image/*" @change="onChange">
        <!--<button type="submit" class="btn btn-primary">Add Avatar</button>-->
        <!--this is omitted due to JS image-->
    </form>

    <img :src="avatar" width="50" height="50">
</div>
</template>


<script>
    export default {
        props: ['user'],

        data() {
            return {
                avatar: '',
            }
        },

        computed: {
            canUpdate() {
                return this.authorize(user => user.id === this.user.id)
                // the user.id from the app.js, same as the props user.id?
                // returns a boolean
            }
        },

        methods: {
            onChange(e) {
                console.log(e);
                if (! e.target.files.length) return;
                let avatar = e.target.files[0];
                let reader = new FileReader();
                reader.readAsDataURL(avatar);
                reader.onload = e => {
                    //console.log(e);
                    this.avatar = e.target.result;
                };
                //persist to server
                this.persist(avatar);
            },

            persist(avatar){
                let data = new FormData();

                data.append('avatar', avatar);

                axios.post(`/api/users/${this.user.name}/avatar`, data)
                    .then(() => flash('Avatar uploaded!'));
                    //
            }
        }
    }
</script>