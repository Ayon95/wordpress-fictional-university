import { registerBlockType } from "@wordpress/blocks";
import { Button, PanelBody, PanelRow } from "@wordpress/components";

console.log(dataFromWp.theme_images_folder_path);

import {
  InnerBlocks,
  InspectorControls,
  MediaUpload,
  MediaUploadCheck,
} from "@wordpress/block-editor";

import { useEffect } from "@wordpress/element";
import apiFetch from "@wordpress/api-fetch";

import bannerFallbackImage from "../images/library-hero.jpg";

registerBlockType("fictional-university/slide", {
  title: "Slide",
  // If this block is added in the post editor, it will occupy full width of the screen
  supports: {
    align: ["full"],
  },
  attributes: {
    align: { type: "string", default: "full" },
    imageId: { type: "number" },
    imageUrl: { type: "string", default: bannerFallbackImage },
    // the theme will come with some default slides with images
    // We need the path to the theme's images folder (coming from PHP) and the filename of a default image
    // This way, our slideshow code will be resilient; it will work no matter where WordPress is installed
    // Initially, this default image will be used
    // If the user uploads their own image, then this default image will be discarded
    defaultImageFilename: { type: "string" },
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
    if (attributes.defaultImageFilename) {
      setAttributes({
        imageUrl: `${dataFromWp.theme_images_folder_path}/${attributes.defaultImageFilename}`,
      });
    }
  }, []);

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
            defaultImageFilename: "",
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
      <div
        className="hero-slider__slide"
        style={{
          backgroundImage: `url('${attributes.imageUrl}')`,
        }}
      >
        <div className="hero-slider__interior container">
          <div className="hero-slider__overlay t-center">
            <InnerBlocks
              allowedBlocks={[
                "fictional-university/generic-heading",
                "fictional-university/generic-button",
              ]}
            />
          </div>
        </div>
      </div>
    </>
  );
}
