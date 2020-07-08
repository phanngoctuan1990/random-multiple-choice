<template>
    <div>
        <div class="mt-4">
            <div class="row">
                <div class="col-md-8 offset-2 text-center">
                    <input
                        id="file"
                        type="file"
                        @change="importFile"
                        aria-describedby="file"
                        class="custom-file-input"
                        accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                    />
                    <label for="file" class="custom-file-label">
                        Select excel file
                    </label>
                </div>
            </div>
            <div v-show="sheet_names.length" class="row mt-4 text-center">
                <div class="col-md">
                    <label>Number rows random for: </label>
                    <div class="form-check-inline">
                        <label class="form-check-label font-weight-bold">
                            <input
                                value="0"
                                type="radio"
                                name="random_type"
                                v-model="random_type"
                                class="form-check-input"
                            />
                            All sheets
                        </label>
                    </div>
                    <div class="form-check-inline">
                        <label class="form-check-label font-weight-bold">
                            <input
                                value="1"
                                type="radio"
                                name="random_type"
                                v-model="random_type"
                                class="form-check-input"
                            />
                            Each sheet
                        </label>
                    </div>
                </div>
            </div>
            <div v-show="sheet_names.length" class="row mt-4">
                <div class="col-md text-center">
                    <table class="table">
                        <thead class="thead-dark">
                            <tr>
                                <th
                                    v-for="(sheet, index) in sheet_names"
                                    class="font-weight-bold"
                                    :key="index"
                                >
                                    {{ sheet.name }}
                                </th>
                            </tr>
                        </thead>
                        <tbody v-show="random_type == 1">
                            <tr>
                                <td
                                    v-for="(sheet, index) in sheet_names"
                                    :key="index"
                                >
                                    <input
                                        type="text"
                                        class="form-control"
                                        v-model="sheet.num_random"
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div
                v-show="sheet_names.length && random_type == 0"
                class="row mt-4 text-center offset-4"
            >
                <div class="col-md-6 form-group">
                    <input
                        type="text"
                        class="form-control"
                        v-model="number_random"
                        placeholder="Number rows to random"
                    />
                </div>
            </div>
        </div>

        <div class="mt-4">
            <ul class="nav justify-content-center">
                <li class="nav-item">
                    <button
                        type="button"
                        @click="handlerGenerate"
                        class="btn btn-success btn-lg btn-block"
                    >
                        Generate File
                    </button>
                </li>
            </ul>
        </div>
        <Toasts
            :rtl="false"
            :max-messages="5"
            :time-out="3000"
            :closeable="true"
            :show-progress="true"
        ></Toasts>
    </div>
</template>

<script>
import XLSX from "xlsx";

export default {
    name: "Home",
    data() {
        return {
            file: null,
            sheet_names: [],
            random_type: 0,
            number_random: ""
        };
    },
    methods: {
        importFile(e) {
            this.sheet_names = [];
            this.random_type = 0;

            const files = e.target.files;
            if (files && files[0]) {
                this.file = files[0];
                this.getSheetsName(this.file);
            }
        },
        getSheetsName(file) {
            const reader = new FileReader();
            reader.onload = e => {
                /* Parse data */
                const bstr = e.target.result;
                const wb = XLSX.read(bstr, { type: "binary" });
                wb.SheetNames.map(name => {
                    this.sheet_names.push({
                        name: name,
                        num_random: 0,
                        data: null
                    });
                });
            };
            reader.readAsBinaryString(file);
        },
        readFile(file) {
            const reader = new FileReader();
            reader.onload = e => {
                /* Parse data */
                const bstr = e.target.result;
                const wb = XLSX.read(bstr, { type: "binary" });
                /* Get first worksheet */
                this.sheet_names.map(sheet => {
                    if (parseInt(sheet.num_random) > 0) {
                        const wsname = sheet.name;
                        console.log(wsname);

                        const ws = wb.Sheets[wsname];
                        /* Convert array of arrays */
                        const rows = XLSX.utils.sheet_to_json(ws, {
                            header: 1
                        });
                        console.log(rows);
                        if (
                            parseInt(sheet.num_random) >
                            (rows.length - 1) / 4
                        ) {
                            sheet.data = rows.shift();
                        }
                        console.log(sheet);

                        /* Update state */
                        // this.data = data;
                        // this.cols = make_cols(ws["!ref"]);
                        // console.log(this.data);
                    }
                });
            };
            reader.readAsBinaryString(file);
        },
        handlerGenerate() {
            this.$toast.info("This feature is comming soon!!!");
            if (this.random_type == 0) {
                this.sheet_names.map(sheet => {
                    sheet.number_random = this.number_random;
                });
            }
            this.readFile(this.file);
        }
    }
};
</script>
