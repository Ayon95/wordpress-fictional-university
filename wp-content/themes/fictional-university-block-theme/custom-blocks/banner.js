import { registerBlockType } from "@wordpress/blocks";
import { Button, PanelBody, PanelRow } from "@wordpress/components";

import {
  InnerBlocks,
  InspectorControls,
  MediaUpload,
  MediaUploadCheck,
} from "@wordpress/block-editor";

import { useEffect } from "@wordpress/element";
import apiFetch from "@wordpress/api-fetch";

import bannerFallbackImage from "../images/library-hero.jpg";

registerBlockType("fictional-university/banner", {
  title: "Banner",
  // If this block is added in the post editor, it will occupy full width of the screen
  supports: {
    align: ["full"],
  },
  attributes: {
    align: { type: "string", default: "full" },
    imageId: { type: "number" },
    imageUrl: { type: "string", default: bannerFallbackImage },
  },
  save: SaveComponent,
  edit: EditComponent,
});

function SaveComponent() {
  return <InnerBlocks.Content />;
}

function EditComponent({ attributes, setAttributes }) {
  function handleMediaFileSelect(media) {
    setAttributes({ imageId: media.id });
  }

  useEffect(() => {
    (async function () {
      try {
        if (attributes.imageId) {
          const response = await apiFetch({
            path: `/wp/v2/media/${attributes.imageId}`,
            method: "GET",
          });
          setAttributes({
            imageUrl: response.media_details.sizes.page_banner.source_url,
          });
        }
      } catch (error) {
        console.error(error);
      }
    })();
  }, [attributes.imageId]);

  return (
    <>
      <InspectorControls>
        <PanelBody title="Background" initialOpen={true}>
          <PanelRow>
            <MediaUploadCheck>
              <MediaUpload
                value={attributes.imageId}
                onSelect={handleMediaFileSelect}
                render={({ open }) => (
                  <Button onClick={open}>Choose Image</Button>
                )}
              />
            </MediaUploadCheck>
          </PanelRow>
        </PanelBody>
      </InspectorControls>
      <div className="page-banner">
        <div
          className="page-banner__bg-image"
          style={{
            backgroundImage: `url('${attributes.imageUrl}')`,
          }}
        ></div>
        <div className="page-banner__content container t-center c-white">
          <InnerBlocks
            allowedBlocks={[
              "fictional-university/generic-heading",
              "fictional-university/generic-button",
            ]}
          />
        </div>
      </div>
    </>
  );
}
