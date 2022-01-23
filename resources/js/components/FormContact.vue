<template>
    <div class="mt-5">
        <h1>Форма обратной связи</h1>
        <div class="row mt-3">
            <input type="hidden" name="name">
            <input type="hidden" name="code" value="">
            <div class="col-md-6">
                <label for="form_firstname" class="form-label">Имя (обязательно)</label>
                <input v-model="model.firstName" type="text" class="form-control" :class="{'is-invalid': error.firstName}" id="form_firstname" value="" required>
                <div class="invalid-feedback">{{error.firstName}}</div>
            </div>
            <div class="col-md-6">
                <label for="form_lastname" class="form-label">Фамилия (обязательно)</label>
                <input v-model="model.lastName" type="text" class="form-control" :class="{'is-invalid': error.lastName}" id="form_lastname" value="" required>
                <div class="invalid-feedback">{{error.lastName}}</div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <label for="form_phone" class="form-label">Телефон(Обязательно)</label>
                <masked-input mask="\+\7 (111) 111-11-11"
                              id="form_phone"
                              @input="model.phone = arguments[1]"
                              class="form-control"
                              :class="{'is-invalid': error.phone}"
                              placeholder=""
                />
                <div class="invalid-feedback">{{error.phone}}</div>

            </div>
            <div class="col-md-6">
                <label for="form_email" class="form-label">E-mail (обязательно)</label>
                <input v-model="model.email" type="email" class="form-control" :class="{'is-invalid': error.email}" id="form_email" required>
                <div class="invalid-feedback">{{error.email}}</div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-12">
                <label for="form_message" class="form-label">Сообщение</label>
                <textarea v-model="model.message" class="form-control" :class="{'is-invalid': error.message}" id="form_message" required></textarea>
                <div class="invalid-feedback">{{error.message}}</div>
            </div>

            <div class="col-12 mt-5">
                <div class="form-check">
                    <input v-model="model.privacy" name="privacy" class="form-check-input" :class="{'is-invalid': error.privacy}" type="checkbox" value="" id="invalidCheck3" aria-describedby="invalidCheck3Feedback" required>
                    <label class="form-check-label" for="invalidCheck3">
                        Я принимаю <a href="#">условия и соглашения</a>
                    </label>
                </div>
            </div>

            <div class="col-12 mt-5">
                <div @click="sendForm" class="btn btn-primary btn-lg" type="submit">Отправить форму</div>
            </div>

            <div class="col-12 mt-5">
                <div v-show="text_success" class="alert alert-success" role="alert">
                    {{text_success}}
                </div>
                <div v-show="text_error"  class="alert alert-danger" role="alert">
                    {{text_error}}
                </div>
            </div>



        </div>
    </div>
</template>

<script>
    import axios from 'axios'
    import MaskedInput from 'vue-masked-input'
    export default {
        props: ['code'],
        data(){
            return{
                disableSubmit: true,
                model: {
                    code: this.code,
                    firstName: '',
                    lastName: '',
                    phone: '',
                    email: '',
                    message: '',
                    privacy: false
                },
                error: {
                    firstName: false,
                    lastName: false,
                    phone: false,
                    email: false,
                    message: false,
                    privacy: false
                },
                text_success: false,
                text_error: false
            }
        },
        methods: {
            sendForm(){

                this.text_success = false
                this.text_error = false
                this.error.firstName = false
                this.error.lastName = false
                this.error.phone = false
                this.error.email = false
                this.error.message = false
                this.error.privacy = false

                if(this.model.privacy){
                    axios.post('sendform', this.model).then((response) => {
                        console.log('response', response)
                        if(response.data.success){
                            this.text_success = response.data.success
                            setTimeout(() => {
                                this.cleanData()
                                this.text_success = false
                            }, 3000)
                        }
                    })
                        .catch((error) => {

                            if (error.response) {
                                console.error('error', error.response)
                                let dataError = error.response.data
                                console.error('dataError', dataError.errors)
                                if(dataError.errors){
                                    for( let key in dataError.errors){
                                        if(key === 'firstName'){
                                            this.error.firstName = dataError.errors[key][0]
                                        }
                                        if(key === 'lastName'){
                                            this.error.lastName = dataError.errors[key][0]
                                        }
                                        if(key === 'email'){
                                            this.error.email = dataError.errors[key][0]
                                        }
                                        if(key === 'phone'){
                                            this.error.phone = dataError.errors[key][0]
                                        }
                                        if(key === 'message'){
                                            this.error.message = dataError.errors[key][0]
                                        }
                                        if(key === 'privacy'){
                                            this.error.privacy = dataError.errors[key][0]
                                        }
                                    }
                                    this.text_error = 'Проверьте корректность заполнения формы'
                                }
                                // console.error('error_response', error.response.data)
                            }
                        })
                }else{
                    this.error.privacy = true
                    this.text_error = 'Необходимо принять условия соглашения'
                }


            },
            cleanData(){
                this.model.firstName = ''
                this.model.lastName = ''
                this.model.phone = ''
                this.model.email = ''
                this.model.message = ''
                this.model.privacy = false
            }
        },
        mounted() {
            console.log('code', this.code)
        },
        components:{
            MaskedInput
        },
    }
</script>
