
<template>
<div class="container mt-5">
    <div class="row">
        <div class="col-lg-4 mx-md-auto well">
             <h2>Reset your password</h2>
            <form @submit.prevent="reset">
                <input v-model="email" type="email" placeholder="Email" class="form-control" autofocus required>
                <button class="btn btn-primary btn-block">Reset password</button>
            </form>
            <p class="text-center">
                <router-link :to="{name:'signup'}">Register for an account</router-link> &#x2013;
                <router-link :to="{name:'login'}">Login</router-link>
            </p>
        </div>
    </div>
</div> <!-- container -->
</template>

<script>
import {iotlab} from '@/rest'

export default {
  name: 'reset',
  data () {
    return {
      email: '',
    }
  },
  methods: {
    async reset () {
      await iotlab.resetPassword(this.email)
      this.$notify({text: 'An email has been sent to ' + this.email, type: 'success'})
    },
  },
}
</script>

<style scoped>

/**
 * I like to move it move it
 * I like to move it move it
 * I like to move it move it
 * You like to - move it!
 */
@keyframes shake {
  8%, 41% {
    transform: translateX(-10px);
  }
  25%, 58% {
    transform: translateX(10px);
  }
  75% {
    transform: translateX(-5px);
  }
  92% {
    transform: translateX(5px);
  }
  0%, 100% {
    transform: translateX(0);
  }
}
form.error {
  animation: shake .5s;
}

input {
  margin: 5px 0;
}
button {
  margin: 5px 0;
}
.well {
  padding: 25px 40px;
}

</style>
