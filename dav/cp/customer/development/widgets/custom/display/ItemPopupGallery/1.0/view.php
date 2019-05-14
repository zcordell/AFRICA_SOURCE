<div id="rn_<?=$this->instanceID;?>" class="<?= $this->classList ?>">
	<!-- Item Gallery Title -->
	<div class="rn_ItemGalleryTitleContainer">
		<p class="rn_ItemGalleryTitle">
			<?= $this->data['attrs']['title'] ?>
		</p>
	</div>
	<!-- End Item Gallery Title -->
	<!-- Item Gallery Description -->
	<div class="rn_ItemGalleryDescriptionContainer">
		<p class="rn_ItemGalleryDescription">
			<?= $this->data['attrs']['description'] ?>
		</p>
	</div>
	<!-- End Item Gallery Description -->
	<!-- Item Gallery Content -->
	<div class="rn_ItemGalleryContentContainer">
		<div class="rn_ItemGalleryLoadingIndicatorContainer">
			<div class="rn_ItemGalleryLoadingIndicator">
				<img class="rn_ItemGalleryLoadingIndicatorImg" src="/euf/assets/images/loading.gif" />
				<div class="rn_ItemGalleryLoadingIndicatorMsgContainer">
					<?= $this->data['attrs']['loading_msg'] ?>
				</div>
			</div>
		</div>
		<div class="rn_ItemGalleryItemsContainer">
			<!-- Items will be dynamically generated -->
		</div>
		<div class="rn_ItemGalleryPaginationContainer">
			<!-- Pagination links will be dynamically generated -->
		</div>
		<div class="rn_ItemGalleryPopupDialogContainer">
		</div>
	</div>
	<!-- End Item Gallery Content -->
</div>