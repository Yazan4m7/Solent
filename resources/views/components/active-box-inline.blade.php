@props([
'title' => 'button',
'type' => 'button',

])

    <div class="blackbox-dialog-inner">
        <!-- Begin images --><h2 class="dialog-title-black-box">Choose Milling Machine</h2>
        <div class="blackbox-images">
            <div class="blackbox-image-container" onclick="selectImage(this)">
                <img alt="Image 1"  src="https://evolve-files.storage.googleapis.com/wp-content/uploads/2022/08/14145653/z4-01-1422x1536.png" width="150"/>
                <div class="blackbox-badges">
                    <div class="blackbox-badge red">2</div>
                    <div class="blackbox-badge blue">3</div>
                </div>
            </div>
            <div class="blackbox-image-container inactive" onclick="selectImage(this)">
                <img alt="Image 2"  src="https://evolve-files.storage.googleapis.com/wp-content/uploads/2022/08/14145653/z4-01-1422x1536.png" width="150"/>
                <div class="blackbox-badges">
                    <div class="blackbox-badge red">0</div>
                    <div class="blackbox-badge blue">0</div>
                </div>
            </div>
            <div class="blackbox-image-container" onclick="selectImage(this)">
                <img alt="Image 3" src="https://evolve-files.storage.googleapis.com/wp-content/uploads/2022/08/14145653/z4-01-1422x1536.png" width="150"/>
                <div class="blackbox-badges">
                    <div class="blackbox-badge red">0</div>
                    <div class="blackbox-badge blue">0</div>
                </div>
            </div>
        </div>
        <button class=" round-box-btn">
            NEST
        </button>


    
