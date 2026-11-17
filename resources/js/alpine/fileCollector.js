export default function fileCollector(config = {}) {
    return {
        selectedFiles: [],
        maxFiles: config.maxFiles || 10,
        maxFileSize: config.maxFileSize || 10 * 1024 * 1024, // 10MB
        sizeErrorModal: false,
        oversizedFileName: '',
        oversizedFileSize: '',
        errorMessage: '',

        handleFileSelect(event) {
            const newFiles = Array.from(event.target.files);
            this.errorMessage = '';

            for (let file of newFiles) {
                if (file.size > this.maxFileSize) {
                    this.oversizedFileName = file.name;
                    this.oversizedFileSize = this.formatBytes(file.size);
                    this.sizeErrorModal = true;
                    continue;
                }

                if (this.selectedFiles.length >= this.maxFiles) {
                    this.errorMessage = 'You can attach up to ' + this.maxFiles + ' files.';
                    break;
                }

                const isImage = file.type.startsWith('image/');
                const previewUrl = isImage ? URL.createObjectURL(file) : null;

                this.selectedFiles.push({
                    file: file,
                    name: file.name,
                    size: this.formatBytes(file.size),
                    isImage: isImage,
                    previewUrl: previewUrl,
                });
            }

            event.target.value = '';
            this.syncFilesToInput();
        },

        removeFile(index) {
            const removed = this.selectedFiles.splice(index, 1)[0];
            if (removed && removed.previewUrl) {
                URL.revokeObjectURL(removed.previewUrl);
            }
            this.errorMessage = '';
            this.syncFilesToInput();
        },

        syncFilesToInput() {
            if (this.$refs.actualFileInput) {
                const dt = new DataTransfer();
                this.selectedFiles.forEach(item => dt.items.add(item.file));
                this.$refs.actualFileInput.files = dt.files;
            }
        },

        formatBytes(bytes) {
            if (bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
        }
    };
}
