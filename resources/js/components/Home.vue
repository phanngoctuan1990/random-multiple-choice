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
            <div v-show="sheets_random.length" class="row mt-4 text-center">
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
            <div v-show="sheets_random.length" class="row mt-4">
                <div class="col-md text-center">
                    <table class="table">
                        <thead class="thead-dark">
                            <tr>
                                <th
                                    v-for="(sheet, index) in sheets_random"
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
                                    v-for="(sheet, index) in sheets_random"
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
                v-show="sheets_random.length && random_type == 0"
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
                <li class="nav-item mr-1">
                    <button
                        type="button"
                        @click="handlerGenerate"
                        class="btn btn-success btn btn-block"
                    >
                        Generate File
                    </button>
                </li>
                <li class="nav-item">
                    <button
                        type="button"
                        @click="handlerReset"
                        class="btn btn-warning btn btn-block"
                    >
                        Reset
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
import http from "../services/http";

export default {
    name: "Home",
    data() {
        return {
            file: null,
            sheets_random: [],
            random_type: 0,
            number_random: ""
        };
    },
    methods: {
        importFile(e) {
            this.sheets_random = [];
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
                    this.sheets_random.push({
                        name: name,
                        num_random: 0,
                        data: []
                    });
                });
            };
            reader.readAsBinaryString(file);
        },
        readFile(file) {
            return new Promise((resolve, reject) => {
                let result = [];
                const reader = new FileReader();
                reader.onload = e => {
                    /* Parse data */
                    const bstr = e.target.result;
                    const wb = XLSX.read(bstr, { type: "binary" });
                    /* Get first worksheet */
                    this.sheets_random.map(sheet => {
                        if (parseInt(sheet.num_random) > 0) {
                            const wsname = sheet.name;
                            const ws = wb.Sheets[wsname];

                            /* Convert array of arrays */
                            const rows = XLSX.utils.sheet_to_json(ws, {
                                header: 1
                            });
                            rows.shift();
                            sheet.data = rows;
                            result.push(sheet);
                        }
                    });
                    resolve(result);
                };
                reader.readAsBinaryString(file);
            });
        },
        handlerReset() {
            this.number_random = "";
            this.sheets_random.map(sheet => {
                sheet.num_random = 0;
            });
        },
        handlerGenerate() {
            if (!this.validate()) {
                this.$toast.error("Please enter number random");
                return;
            }

            if (this.random_type == 0) {
                this.sheets_random.map(sheet => {
                    sheet.num_random = this.number_random;
                });
            }

            this.readFile(this.file).then(res => {
                this.sendToServe(res).then(res => {
                    var newBlob = new Blob([res], {
                        type: "application/pdf"
                    });
                    const data = window.URL.createObjectURL(newBlob);
                    var link = document.createElement("a");
                    link.href = data;
                    link.download = "multiple_choices.pdf";
                    link.click();
                });
            });
        },
        validate() {
            switch (this.random_type) {
                case "1":
                    let sheets = this.sheets_random.filter(
                        sheet =>
                            sheet.num_random && parseInt(sheet.num_random) > 0
                    );
                    return sheets.length > 0;

                default:
                    return this.number_random && this.number_random != 0;
            }
        },
        async sendToServe(payload) {
            try {
                const { data } = await http.post(
                    "/generate-multiple-choices",
                    {
                        generate_data: payload
                    },
                    { responseType: "arraybuffer" }
                );
                return data;
            } catch (error) {
                console.error(error);
            }
        }
    }
};
</script>
