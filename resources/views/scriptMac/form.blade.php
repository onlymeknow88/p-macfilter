<!-- Modal -->
<div class="modal fade" id="modal-form" tabindex="-1" aria-labelledby="modal-form" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" class="form">
                    @method('post')
                    @csrf

                    <div class="mb-3">
                        <label for="mac_address" class="form-label">Mac Address</label>
                        <input type="text" name="mac_address" class="form-control" id="mac_address"
                            placeholder="00-00-00-00-00-00" maxLength="17">
                    </div>
                    <div class="mb-3">
                        <label for="computer_name" class="form-label">Computer Name</label>
                        <input type="text" name="computer_name" class="form-control" id="computer_name">
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" id="username">
                    </div>
                    <div class="mb-3">
                        <label for="lan_wan" class="form-label">Lan/Wan</label>
                        <select name="lan_wan" class="form-select" id="lan_wan">
                            <option value="L">
                                LAN
                            </option>
                            <option value="W">
                                WAN
                            </option>
                        </select>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="submitForm(this.form)">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>
