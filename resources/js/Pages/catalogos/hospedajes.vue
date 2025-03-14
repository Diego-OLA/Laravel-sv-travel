<script setup>
import { Head } from '@inertiajs/vue3';
import { computed, onMounted, ref } from "vue";
import { usePage } from "@inertiajs/vue3";  
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/css";
import { ToastService } from 'primevue';
import { useToast } from 'primevue/usetoast';
import "swiper/css/navigation";
import { Autoplay, Navigation } from "swiper/modules";


import { FilterMatchMode } from '@primevue/core/api';
//import { useToast } from 'primevue/usetoast';
import axios from "axios";
//import Swal from "sweetalert2";
import Toolbar  from 'primevue/toolbar';
import DataTable  from 'primevue/datatable';
import Column from 'primevue/column';
import Dialog from 'primevue/dialog';
import InputIcon from 'primevue/inputicon';
import IconField from 'primevue/iconfield';
import InputText from 'primevue/inputtext';
import Button from "primevue/button";
import Textarea from 'primevue/textarea';
import FileUpload from 'primevue/fileupload';
import InputNumber from 'primevue/inputnumber';
import AutoComplete from 'primevue/autocomplete';
import Select from 'primevue/select';

    const page = usePage();
    //const toast = useToast();
    const user = page.props.auth.user;
    const isOpen = ref(false);
    const urlBase = 'http://127.0.0.1:8000/api/' //se debe de declarar la url porque de esa fomra se le hace la peticion a axios 
    const hospedajes = ref([]);
    const hospedaje = ref({});
    const hospedajesUser = ref([]);
    //const toast = useToast();
    const dt = ref();
    const dialog = ref(false);
    const selectedHospedajes =  ref();
    const deleteHospedajeDialog = ref(false);
    const fileUploadRef = ref(null);
    const showImageDialog = ref(false);
    const imagesPreview = ref([]); // arreglo de imagenes para la peticion 
    const imagenes = ref([]);
    const filters = ref({
    'global': {value: null, matchMode: FilterMatchMode.CONTAINS},
});
const submitted = ref(false);

const openNew = () => {
    hospedaje.value = {};
    clearFiles();
    submitted.value = false;
    dialog.value = true;
   
};
const formatCurrency = (value) => {
    if(value)
        return value.toLocaleString('en-US', {style: 'currency', currency: 'USD'});
    return;
};
const hideDialog = () => {
    dialog.value = false;
  
    submitted.value = false;
   
};
//funcion para el manejo de las imagenes
const onImageSelect = (event)=>{
    imagenes.value = [];
    imagesPreview.value=[];

    for(const file of event.files ){
        imagenes.value.push(file);//se agregar las imagenes que se enviaran en la peticion
        const reader = new FileReader();
        reader.onload = (e) => {
            imagesPreview.value.push(e.target.result)
        }   
        reader.readAsDataURL(file);
    }
}

const removeImage = (index)=>{
    imagenes.value.splice(index,1)
    imagesPreview.value.splice(index,1);
};

const clearFiles = ()=>{
if(fileUploadRef.value){
    fileUploadRef.value.clear();
}
imagenes.value = [];
imagesPreview.value = [];
};
const saveOrUpdate = async() => {
    submitted.value = true;

    if (hospedaje?.value.descripcion?.trim()) {
        if (hospedaje.value.id) {
          
           
        }
        else {
           
            try{
            const formData = new FormData();
                hospedaje.value.user= user
            formData.append('hospedaje',JSON.stringify(hospedaje.value));
            //agregamos las imagenes al formData
            imagenes.value.forEach((imagen)=>{
                formData.append("imagenes[]",imagen);
            })
          /*  formData.forEach((value,key)=>{
                console.log(key,value)
            })*/

            const response = await axios.post(urlBase+"hospedajes",formData,{headers: {"Content-Type": "multipart/form-data"}})
            if(response.status ===201){
                console.log(response.data);
                const {hospedaje: nuevoHospedaje,message}= response.data;
                hospedajes.value.unshift(nuevoHospedaje.original);
               // toast.add({severity:'success', summary: 'Registrado', detail: message, life: 3000});
            }
            dialog.value = false;
            hospedaje.value = {};
           }catch(err){
            if(err.response && err.response.status===409){
                const {message}=err.response.data;
              //  toast.add({severity:'warn', summary: 'Conflicto', detail: message, life: 3000});
            }else if(err.response && err.response.status===500){
                const {message}=err.response.data;
               // toast.add({severity:'error', summary: 'Error', detail: error, life: 3000});
            }else{
                console.log("Error inesperado",err);
            }
           }
        }

        dialog.value = false;
        hospedaje.value = {};
    }
};
const editHospedaje = (hos) => {
    hospedaje.value = {...hos};
    dialog.value = true;
};
const confirmHospedaje = (hos) => {
    hospedaje.value = hos;
    deleteHospedajeDialog.value = true;
};




    
    onMounted(() => { //este metodo hace una peticion para llenar los datos y carga los componentes
   fetchHospedajesUs();
})

const fetchHospedajesUs = async() =>{
    try {
      const response =  await axios.get(`${urlBase}registros/`+user.id) //se esta utilizando la url base
      hospedajes.value = response.data;
     

    }catch(err){
        console.error("error", err);
    }
}



const exportCSV = () => {
    dt.value.exportCSV();
};

const dialogTitle = computed(()=>
    hospedaje.value.id ? "Actualización de hospedaje" : "Registro de hospedaje"
);

const btnTitle = computed(()=>
    hospedaje.value.id ? "Actualizar" : "Agregar"
);

</script>
<template>
    <div class="bg-white">
         <div class="container mx-auto px-4 py-4 flex flex-col md:flex-row justify-between items-center">
            <!-- Logo -->
            <div class="mb-4 md:mb-0">
                <a href="/"><h1 class="text-3xl font-bold text-blue-900">SV Travel</h1></a>
            </div>
            
            <!-- Search and Buttons -->
            <div class="shadow-blue-500 flex flex-row md:flex-row items-center space-y-4 md:space-y-0 md:space-x-4 bg-white rounded-full shadow-lg px-6 py-3 " >
                <div class="flex items-center">
                    <label for="search" class="mr-2 text-gray-700">Busca tu hospedaje</label>
                    <input type="text" id="search" placeholder="buscar" class="border border-gray-300 px-3 py-1 rounded">
                </div>
                <div class="flex space-x-2 ">
                    <div class="hidden md:flex">
                    <button class="bg-white text-blue-700 border border-blue-700 lg:px-2 lg:py-1 rounded hover:bg-blue-50 transition mr-2">Gestiona tus alojamientos</button>
                    <div :class= "{ 'block':isOpen}" >
                        <div v-if="!user" class="flex flex-col lg:flex-row lg:space-x-4 text-center mt-4 lg:mt-0" >
                            <a href="/register"> <button class="bg-white text-blue-700 border border-blue-700 px-2srounded hover:bg-blue-50 transition">Registrarse</button></a>
                            <a href="/login"> <button class="bg-white text-blue-700 border border-blue-700 px-2 rounded hover:bg-blue-50 transition">Iniciar Sesión</button></a>
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
        <div class="mx-2 mt-7" >
            <h3 class="bg-blue-500 font-bold text-center text-white text-xl px-1 rounded" >Gestiona y agrega nuevos alojamientos</h3>
           
              <!--  <div  v-for="hospedaje in hospedajes" :key="hospedajes.id" class="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition py-2">
                    <div v-if="user.id==hospedaje.user_id">
                        <Swiper :modules="[Navigation]" navigation class="h-40">
                            <SwiperSlide v-for="img in hospedaje.imagenes" :key="img">
                            <img :src="`/images/hospedajes/${img.nombre}`" class="w-full h-40 object-contain" />
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
                            <h3 class="font-bold text-lg mb-2">{{ hospedaje.descripcion }}</h3>
                            <p class="text-gray-600 mb-2">{{ hospedaje.direccion }}</p>
                            <p class="font-bold text-blue-600   ">{{ hospedaje.precio }}<span class="font-normal text-gray-600">/ noche</span></p>
                        </div>
                    </div>
                    <div v-else class=""></div>
                  </div>
                  -->

            <div class="h-full rounded">
                <div class="card w-1/1 px-10 py-10 shadow-blue-600 shadow-lg rounded">
                        <Toolbar class="mb-6 ">
                            <template #start>
                                <Button label="Nuevo Hospedaje" icon="pi pi-plus" class="mr-2" @click="openNew" />
                            </template>

                            <template #end>
                             <!--  <FileUpload mode="basic" accept="image/*" :maxFileSize="1000000" label="Import" customUpload chooseLabel="Import" class="mr-2" auto :chooseButtonProps="{ severity: 'secondary' }" />--> 
                                <Button label="Export" icon="pi pi-upload" severity="secondary" @click="exportCSV($event)" />
                            </template>
                        </Toolbar>

                    <DataTable
                        ref="dt"
                        v-model:selection="selectedHospedajes"
                        :value="hospedajes"
                        dataKey="id"
                        :paginator="true"
                        :rows="10"
                        :filters="filters"
                        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                        :rowsPerPageOptions="[5, 10, 25]"
                        currentPageReportTemplate="Mostrando del {first} a {last} de {totalRecords} hospedajes"
                    >
                        <template #header>
                            <div class="flex flex-wrap gap-2 items-center justify-between ">
                                <h4 class="m-0">Gestión Hospedajes</h4>
                                <IconField>
                                    <InputIcon>
                                        <i class="pi pi-search" />
                                    </InputIcon>
                                    <InputText v-model="filters['global'].value" placeholder="Buscar..." />
                                </IconField>
                            </div>
                        </template>
                        <Column  field="descripcion" header="Descripción" sortable style="min-width: 16rem"></Column>
                        <Column field="direccion" header="Direccion" sortable style="min-width: 16rem"></Column>
                        <Column field="cantidad_huespedes" header="Huespedes" sortable style="min-width: 16rem"></Column>
                        <Column field="precio" header="Precio" sortable style="min-width: 8rem">
                            <template #body="slotProps">
                                {{ formatCurrency(slotProps.data.precio) }}
                            </template>
                        </Column>
                       
                    <!--
                        <Column field="inventoryStatus" header="Status" sortable style="min-width: 12rem">
                            <template #body="slotProps">
                                <Tag :value="slotProps.data.inventoryStatus" :severity="getStatusLabel(slotProps.data.inventoryStatus)" />
                            </template>
                        </Column>-->
                        <Column :exportable="false" style="min-width: 12rem">
                            <template #body="slotProps">
                               <!-- <Button icon="pi pi-images" outlined rounded severity="info" class="mr-2" @click="viewImages(slotProps.data)" />-->
                                <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editHospedaje(slotProps.data)" />
                                <Button icon="pi pi-trash" outlined rounded severity="danger" @click="confirmDeleteHospedaje(slotProps.data)" />
                            </template>
                        </Column>
                    </DataTable>
                </div>
                
                  </div>
           
        </div>
    </div>
    <Dialog v-model:visible="dialog" :style="{ width: '450px' }" :header="dialogTitle" :modal="true">
            <div class="flex flex-col gap-6">
                <div>
                    <label for="descripcion" class="block font-bold mb-3">Descripción</label>
                    <InputText id="descripcion" v-model.trim="hospedaje.descripcion" required="true" autofocus :invalid="submitted && !hospedaje.descripcion" fluid />
                    <small v-if="submitted && !hospedaje.descripcion" class="text-red-500">Descripción es requerido.</small>
                </div>
                <div>
                    <label for="direccion" class="block font-bold mb-3, px-3" >Direccion</label>
                    <Textarea id="direccion" v-model="hospedaje.direccion" required="true" rows="2" cols="20" fluid />
                    <small v-if="submitted && !hospedaje.descripcion" class="text-red-500">Dirección es requerido.</small>
                </div>
             
                    <div class="col-span-6">
                        <label for="precio" class="px-3">Precio</label>
                        <InputNumber id="precio" v-model.trim="hospedaje.precio" fluid/>
                        <small class="p-error text-red-500" v-if="submitted && !hospedaje.precio">precio es requerido</small>
                      
                    </div>

                    <div class="col-span-6">
                        <label for="huspedes" class="px-3">Cantidad de huspedes</label>
                       <InputNumber id="huspedes" v-model.trim="hospedaje.cantidad_huespedes" 
                        :class="{'p-invaid:' : submitted && !hospedaje.cantidad_huespedes}"/>
                        <small class="p-error text-red-500" v-if="submitted && !hospedaje.cantidad_huespedes">Huespedes es requerido</small>
                    </div>
           
              
                   
                
               
                    <div class="col-span-6">
                        <label for="checkin" class="px-7">Check-in</label>
                        <InputText id="checkin" v-model="hospedaje.checkin"
                        :class="{'p-invaid:' : submitted && !hospedaje.checkin}"/>
                        <small class="p-error text-red-500" v-if="submitted && !hospedaje.checkin">checkin es requerido</small>
                    </div>
           
                    <div class="col-span-6">
                        <label for="checkout" class="px-6">Check-out</label>
                        <InputText id="checkout" v-model="hospedaje.checkout"
                        :class="{'p-invaid:' : submitted && !hospedaje.checkout}"/>
                        <small class="p-error text-red-500" v-if="submitted && !hospedaje.checkout">checkout es requerido</small>
                    </div>
                   

                </div>
                <div class="w-full">               
                    <label for="" class="block font-bold mb-3">Imagenes</label>
                    <FileUpload class="w-10/12" ref="fileUploadRef" mode="basic" accept="image/*" custom-upload multiple @select="onImageSelect" choose-label="Seleccionar Imagenes"/>
                </div>
              <!--Div para vista previa imagenes-->
                <div v-if="imagesPreview.length" class="grid grid-cols-3 gap-2" >
                    <div v-for="(img,index) in imagesPreview" :key="index" class="relative group">
                        <img :src="img" class="w-full h-24 object-contain rounded shadow">
                    <Button icon="pi pi-times" class="absolute top-0 right-0 p-1 text-white rounded-full bg-fuchsia-950 opacity-0 group-hover:opacity-100 transition"
                    @click="removeImage(index)" />

                    </div>

            </div>
            
         

            

           

            <template #footer>
                <Button label="Cancel" icon="pi pi-times" text @click="hideDialog" />
                <Button :label="btnTitle" icon="pi pi-check" @click="saveOrUpdate" />
            </template>

        </Dialog>

</template>
