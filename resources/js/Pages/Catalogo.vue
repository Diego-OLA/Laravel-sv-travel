<script setup>

import { computed, onMounted, ref } from "vue";
import { usePage } from "@inertiajs/vue3";  
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/css";
import "swiper/css/navigation";
import { Autoplay, Navigation } from "swiper/modules";
import axios from "axios";
import Dialog from 'primevue/dialog';
import PrimeVue from 'primevue/config';
import Aura from '@primeuix/themes/aura';
import { ToastService } from 'primevue';

import VueTailwindDatepicker from 'vue-tailwind-datepicker';


//import Swal from "sweetalert2";

    const dialog = ref(false);
    const page = usePage();
    const user = page.props.auth.user;
    const isOpen = ref(false);
    const urlBase = 'http://127.0.0.1:8000/api/' //se debe de declarar la url porque de esa fomra se le hace la peticion a axios 
    const hospedajes = ref([]);
    const hospedajeGet = ref();
    const selectedDateIn = ref();
    const selectedDateSal = ref();
   const dateRange = ref([]);

   const destacados = ref([
       {id:1, imagen: "/images/slider/lugares1.png" },
       { id:2, imagen: "/images/slider/lugares2.png" },
       {id:3,  imagen: "/images/slider/lugares3.png" },
       { id:4, imagen: "/images/slider/Playa.jpg" },
       { id:5, imagen: "/images/slider/tunco.jpg" }
       

  
]);

    onMounted(() => { //este metodo hace una peticion para llenar los datos y carga los componentes
   fetchHospedajes();
   
})

    const logOut = async () => {
    try {
        await axios.post("/logout");
        window.location.href = "/login"; // Redirigir al login después de cerrar sesión
    } catch (error) {
        console.error("Error al cerrar sesión", error);
    }
};
    const fetchHospedajes = async() =>{
    try {
      const response =  await axios.get(`${urlBase}hospedajes`) //se esta utilizando la url base
      hospedajes.value = response.data;
     

    }catch(err){
        console.error("error", err);
    }
}


const hideDialog = () => {
    dialog.value = false;
    submitted.value = false;
  
};
const openNew = async(id) => {

    try {
      const response =  await axios.get(`${urlBase}hospedajes/`+id) //se esta utilizando la url base
      hospedajeGet.value = response.data;
    
      dialog.value = true;
     

    }catch(err){
        console.error("error", err);
    }    
   


};

const filteredReservas = computed(()=>{
  
})

const minFecha = new Date();

let año = minFecha.getFullYear();
let mes =  minFecha.getMonth()+1;
let mesSin = minFecha.getMonth()+7;
let dia = minFecha.getDate()

if(mes<=9){
    mes = "0"+mes
}
let mesMax = mes.substring(0,1);
mesMax = mesMax+mesSin


let fechaC = año+"-"+mes+"-"+dia
let fechaMax =  año+"-"+mesMax+"-"+dia

function fechas() {
    let fechaSal = document.querySelector("#fechaSal")
    let fechaIn = document.querySelector("#fechaIn")
    console.log(selectedDateIn.value+"   "+selectedDateSal.value)
}



</script>
<template>
    <div class="bg-white">
    
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
                            <span class="py-2 lg:py-0 text-black">{{ user.name }}</span>
                             <button class="bg-blue-500 px-4 py-2  rounded w-full lg:w-auto" @click="logOut()" >Cerrar Sesión</button>
                        </div> 
                       
                    </div>
                </div>
            </div>
        </div>
        </div>
        <div class="container mx-auto px-4 py-6">
            <!-- Banner Image -->
            <Swiper :modules="[Navigation, Autoplay]" navigation :autoplay="{ delay: 3000, disableOnInteraction: false }"
            :speed="1000" loop class="my-6">
            <SwiperSlide v-for="destacado in destacados" :key="destacado.id" class="p-4">
                <div class="bg-gray-800 text-white rounded-lg overflow-hidden shadow-lg">
                <img :src="destacado.imagen" class="w-full h-56 object-cover object-center" />
                </div>
      </SwiperSlide>
    </Swiper>
            <p class="text-center font-semibold text-black">Hospedate en lugares increibles en cualquier parte de El Salvador y explora.</p>
        </div>
        <div class="mb-12 ml-2">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Hospedajes Destacados</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Accommodation 1 -->
                    <div  v-for="hospedaje in hospedajes" :key="hospedaje.id" class="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition">
                        <Swiper :modules="[Navigation]" navigation class="h-40">
                           <SwiperSlide v-for="img in hospedaje.imagenes" :key="img">
                            <img @click="openNew(hospedaje.id)" :src="`/images/hospedajes/${img.nombre}`" class="w-full h-40 object-contain" />
                            </SwiperSlide>
                            
                        </Swiper>
                        <div class="p-4">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-blue-600">Hospedaje</span>
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    <span class="text-sm text-gray-600 ml-1">4.8</span>
                                </div>
                            </div>
                            <a href="/reservas"> <h3 class="font-bold text-lg mb-2 text-blue-600" >{{ hospedaje.descripcion }}</h3> </a>
                            <p class="text-gray-600 mb-2">{{ hospedaje.direccion }}</p>
                            <p class="font-bold text-blue-600">{{ hospedaje.precio }}<span class="font-normal text-gray-600">/ noche</span></p>
                           
                                                    
                            <Dialog v-model:visible="dialog" :style="{ width: '750px' }" header="Hospedaje" :modal="true">
                                <div class="  flex items-center gap-4 flex-col text-left">
                                    <Swiper :modules="[Navigation]" navigation class="h-40">
                                        <SwiperSlide class="" v-for="img in hospedajeGet.imagenes" :key="img">
                                        <img :src="`/images/hospedajes/${img.nombre}`" class="w-full h-40 object-contain" />
                                        </SwiperSlide>
                                                    
                                    </Swiper>
                                    <div class="grid grid-cols-2 gap-2">
                                    <p>Descripcion: </p> <p>{{ hospedajeGet.descripcion }}</p>
                                    <p>Dirección:</p>{{ hospedajeGet.direccion }}
                                    <p>Huespedes: </p>{{ hospedajeGet.cantidad_huespedes }}
                                    <p>Precio por noche: </p><p>{{ hospedajeGet.precio }}</p>
                                    <label for="Fecha de ingreso">Fecha de ingreso:</label>
                                    <input class="bg-slate-600 rounded" v-model="selectedDateIn"  type="date" :min=fechaC :max=fechaMax id="fechaIn" value="ola" > 
                                    <label for="Fecha de salida">Fecha de salida:</label>
                                    <input class="bg-slate-600 rounded"  v-model="selectedDateSal" type="date" :min=fechaC :max=fechaMax id="fechaSal" value="ola" > 
                                    </div>
                                    
                                </div>

                                <VueTailwindDatepicker class="bg-slate-800 text-black  absolute top-full left-0 mt-2 z-10"  :show-year-picker="false" :show-time-picker="false" :popover-class="'w-72 max-w-xs p-4 bg-red shadow-lg z-10 '" v-model="dateRange" :as-single=true :inline="true" />
                                       <button @click="fechas()" class="rounded bg-blue-400 p-1 my-2">Reservar</button> 
                                       
                                      
                                      
                                    
                            </Dialog>
             
                        </div>
                   </div>
            </div>
        </div>
        <div class="h-40 border border-dashed border-black my-10 py-16 relative inline-block    ">
           
        </div>

    </div>
  
  
</template>
      
    
