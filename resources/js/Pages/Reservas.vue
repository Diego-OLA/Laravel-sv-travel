<script setup>

import { computed, onMounted, ref } from "vue";
import { usePage } from "@inertiajs/vue3";  
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/css";
import "swiper/css/navigation";
import { Autoplay, Navigation } from "swiper/modules";
import axios from "axios";
//import Swal from "sweetalert2";

    const page = usePage();
    const user = page.props.auth.user;
    const isOpen = ref(false);
    const urlBase = 'http://127.0.0.1:8000/api/hospedajes/' //se debe de declarar la url porque de esa fomra se le hace la peticion a axios 
   const hospedajes = ref([]);





onMounted (()=>{
  
})
</script>
<template>
   
    <div class="container mx-auto px-4 py-4 flex flex-col md:flex-row justify-between items-center">
            <!-- Logo -->
            <div class="mb-4 md:mb-0">
                <h1 class="text-3xl font-bold text-blue-900">SV Travel</h1>
            </div>
            
            <!-- Search and Buttons -->
            <div class="shadow-blue-500 flex flex-row md:flex-row items-center space-y-4 md:space-y-0 md:space-x-4 bg-white rounded-full shadow-lg px-6 py-3 " >
                <div class="flex items-center">
                    <label for="search" class="mr-2 text-gray-700">Busca tu hospedaje</label>
                    <input type="text" id="search" placeholder="buscar" class="border border-gray-300 px-3 py-1 rounded">
                </div>
                <div class="flex space-x-2 ">
                    <div class="hidden md:flex">
                   <a href="/hospedajes"> <button class="bg-white text-blue-700 border border-blue-700 px-2 py-1 md:px-1 md:py-1  rounded hover:bg-blue-50 transition mr-2">Gestiona tus alojamientos</button></a>
                    <div :class= "{ 'block':isOpen}" >
                        <div v-if="!user" class="flex flex-col lg:flex-row lg:space-x-4 text-center mt-4 lg:mt-0" >
                            <a href="/register"> <button class="bg-white text-blue-700 border border-blue-700 md:px-3 py-1 rounded  md:mb-1  hover:bg-blue-50 transition">Registrarse</button></a>
                            <a href="/login"> <button class="bg-white text-blue-700 border border-blue-700 md:-3 py-1 rounded hover:bg-blue-50 transition">Iniciar Sesión</button></a>
                        </div>
                        <div v-else class="flex flex-row lg:flex-row lg:items-center lg:space-x-4 text-center mt-4 lg:mt-0">
                            <span class="py-2 lg:py-0">{{ user.name }}</span>
                             <button class="bg-blue-500 px-4 py-2  rounded w-full lg:w-auto" @click="logOut()" >Cerrar Sesión</button>
                        </div> 
                    </div>
                </div>
            </div>
        </div>
        </div>
     
</template>