# SBWC VVIC Product Importer Instructions

## SECTION 1: IMPORTING PRODUCTS

### CSV file format & uploading

The most important thing to remember where CSV import files are concerned is that the file itself needs to be formatted correctly, and that the correct number of columns need to be present. 

**The following rules need to be adhered to if an import is to be successful:**

    1. The semicolon (;) needs to be used as file delimiter when saving. 
    2. The following columns need to be present in the file, whether empty or not:
        (a) sku_code –> the product SKU to be used during import [REQUIRED].
        (b) vvic_url –> the vvic product page url [REQUIRED].
        (c) product_title –> the product title you wish to use for initial import [OPTIONAL]. 
            Defaults to product SKU if not present.
        (d) price_usd –> the product’s price in USD [OPTIONAL]. Defaults to VVIC price if 
            not present.
        (e) categories –> comma separated product categories which should be attached to 
            the product on import [REQUIRED]. If the categories provided are not present 
            on the website to which products are being imported, they will be created 
            during import and attached to the product.
        (f) size_chart_image_url –> URL of the product’s size chart image 
            [OPTIONAL BUT RECOMMENDED]. This is used to attach size chart image to product, 
            which is later then used to read image text and create product size chart.
        (g) is_available -> whether or not the product being imported is currently available. 
            Use Y for yes, N for no. Using N means import of a particular product will be skipped
    3. Chinese -> English attribute map must be set up and complete in the admin screen 
            (VVIC Imports -> Attribute Map)
    4. SKU Ending Codes must be set up and complete in the admin screen 
            (VVIC Imports -> SKU Ending Codes)

### IMPORTANT NOTES ON IMPORTING

**Note 1:** Failure to do adhere to the above set of rules will result in failed or partially failed imports, or the import process failing in its entirety.

**Note 2:** Once the above requirements have been met, you can upload your correctly formatted CSV import file on VVIC Imports -> VVIC Imports page. After successful file upload, you may process the CSV file by clicking on Submit CSV for processing button.

**Note 3:** It is recommended that no more than 30 products be imported at a time. In other words, your CSV import file needs to contain a maximum of 30 products for each import. You can add more, but import will take longer, and it is more likely that errors will occur during the import process, such as various bits of product data being missing after the entire import process completes.

**Note 4:** Because of the amount of data which needs to be processed for each product, importing can take a while to complete, typically between 10 and 20 minutes for 30 products.
This means that, while the import process is running, you will likely see partial product data for some products, while others will have a complete data set. This is normal and should not be worried about. 

**The import process flow is split up as follows:**

    1. Products are initially inserted into the database based on data contained in your CSV import
       file;
    2. Product basic metadata is inserted by querying the VVIC product URL provided in your CSV 
        import file and retrieving associated data;
    3. Product main image is retrieved from VVIC website and attached;
    4. Product size chart image, if a link for it is defined in CSV file, is retrieved and attached;
    5. Product categories as provided in CSV file are mapped to each product;
    6. Product attributes as retrieved from VVIC product link is mapped to each product;
    7. Product gallery images are imported and attached to attribute/variation data and attached as 
        product gallery images.

**Note 5:** If any of the above processes fail to complete, for whatever reason, they are rescheduled and will run again. The process which typically takes the longest is the import of product gallery images.

If you are unsure about whether or not the import process is complete, you can navigate to **Tools -> Scheduled Actions** in the WordPress admin page and search for and check the status of the following processes:

    1. vvic_import_products
    2. vvic_import_gallery_imgs
    3. vvic_insert_variation_meta
    4. vvic_retrieve_failed_data (runs for products for which initial VVIC data retrieval failed)
    5. vvic_reimport_gallery_imgs (runs for products for which initial gallery images import failed)

If any of the above processes are still running, or there are multiple instances of these processes running, it means that the import has not completed yet, which means the products you are importing will likely only have partially complete data, until such a time that all processes have been completed.

**Note 6:** *Please do not attempt to perform multiple imports at the same time or in quick succession.* Each import process needs to be completed as covered in **Note 5** above before you begin a new import process.

**Note 7:** There will be instances where certain bits of product data for a particular product is not available from VVIC, for whatever reason. *These products will require the manual entry of missing data.*

## SECTION 2: PARSING SIZE CHART DATA USING OCR

Size chart images are attached to each product during the import process, on condition that a size chart image URL has been provided for that product in the CSV import file, and that the importer was able to successfully retrieve the size chart image during the import process.

These images should be parsed/read to text using OCR (Optical Character Recognition) in either the product edit screen (found under Extract chart image data tab in the VVIC Imported Data metabox at the bottom of the edit screen for each imported product), or by navigating to VVIC Imports -> Crop/Parse Charts page in the WordPress admin area.

An image cropping tool is provided to allow you to split size chart header parameter data and body data into separate images. These areas have to be split into separate images so that the correct data sets are assigned to the correct areas in the parsed size chart table which is displayed on the product page on the front-end of the website. 

Instructions on how to use the image cropper is provided on the pages mentioned above.

Additional instructions on how to parse size chart header and body images to text are provided on these pages as well.

**Before you begin the size chart image cropping/parsing process, the following needs to be set up and complete:**

    1. The chart parameter map (VVIC Imports -> Chart Parameter Map -> Chart Parameter Map tab). 
        This allows the replacement of Chinese chart header parameters with their English chart 
        header parameter counterparts when saving the parsed header data and is required in order
        for the OCR process for chart headers to succeed. If these maps are not present, saving 
        accurate chart header parameters will fail.
    2. The chart parameter replacement map (VVIC Imports -> Chart Parameter Map -> Chart 
        Replacement Map tab). While the OCR parsing process tends to be accurate up to 80% in 
        reading text from images, there are times where it will fail to read Chinese header 
        parameters accurately. Inaccurate or partially accurate parameters will be returned when 
        this happens. These inaccurate or incorrect Chinese parameters will need to be replaced 
        with the correct Chinese parameters so that the correct English parameter is substituted 
        (as defined in point number 1 above) when the parsed chart header data is saved. Failure 
        to set up the chart replacement map will result in blank English header parameters when 
        saving the parsed chart data and/or partially broken size chart tables.

### IMPORTANT NOTES ON USING OCR TO PARSE SIZE CHART IMAGES

**Note 1:** Parsing chart header images takes longer than parsing chart body images. This is due to the fact that several passes are done on these images, with up to 8 different settings being used in order to get the most accurate text returned from the parsing process. This process can take up to a minute to complete, so please be patient.

**Note 2:** Once the process mentioned above is complete, you will need to select the header parameter text which most accurately represents that which is found in the original size chart image. You should select only one of the options provided, otherwise saving the parsed data will fail. Also ensure that any inaccurate characters or terms are present in your chart parameter replacement map so that they can be replaced with the correct Chinese terms in order to facilitate replacing them with the matching English terms during the saving process.

**Note 3:** If you are having accuracy issues with chart header parameters during the parsing/image reading process, recrop your header image using the cropping tool, making sure that you leave enough white space around the edges of the image during the process, then run the OCR parsing process again. This often improves the accuracy of the text which is returned. The same goes for cropping and parsing chart body images.

**Note 4:** Charts which have been parsed should be reviewed for accuracy on VVIC Imports -> Review Charts page once the cropping/parsing process has been completed via either the VVIC Imports -> Crop/Parse page, or the product edit screen as described earlier. Chart data can be adjusted as needed by following the instructions provided in the Review Chart lightbox for each product.
