<div class="modal fade" id="cropperModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Crop Image</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body d-flex gap-3">
        <div class="w-75">
          <img id="cropperImage" class="img-fluid" />
        </div>
        <div class="w-25">
          <div class="preview-wrapper" style="width: 100%; padding-top: 133%; position: relative;">
            <img id="cropPreview" class="img-fluid position-absolute top-0 start-0 w-100 h-100 object-fit-cover" />
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button id="saveCropBtn" class="btn btn-success">Save</button>
      </div>
    </div>
  </div>
</div>
