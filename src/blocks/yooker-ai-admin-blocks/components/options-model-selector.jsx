import { CheckboxControl, TextControl } from '@wordpress/components';
import { useEffect, useState } from 'react';

const OptionsModelSelector = ({ options = [], setOptions, model = [], setModel, price, setPrice }) => {
    const [basePrice, setBasePrice] = useState(price);    

    const optionModelMapping = {
        'text_generate': 'GPT4o mini',
        'image_generate': 'GPT4o',
        'voice_generate': 'GPT4o'
    };

    const optionPriceMapping = {
        'text_generate': 5.00,
        'image_generate': 10.00,
        'voice_generate': 12.50
    };

    const parseJSONField = (field) => {
        if (typeof field === 'string') {
            try {
                return JSON.parse(field);  
            } catch (error) {
                console.error(`Failed to parse field: ${field}`, error);
                return [];  
            }
        }
        return Array.isArray(field) ? field : [];  
    };

    useEffect(() => {
        const selectedModels = new Set();
        let additionalPrice = 0;

        options.forEach((option) => {
            if (optionModelMapping[option]) {
                selectedModels.add(optionModelMapping[option]);
            }
            if (optionPriceMapping[option]) {
                additionalPrice += optionPriceMapping[option];
            }
        });

        setModel([...selectedModels]);
        setPrice(basePrice + additionalPrice);

    }, [options, setModel, setPrice, basePrice]);

    const handleOptionToggle = (selectedOption) => {
        const currentOptions = Array.isArray(options) ? options : parseJSONField(options);

        const updatedOptions = currentOptions.includes(selectedOption)
                ? currentOptions.filter((option) => option !== selectedOption) 
                : [...currentOptions, selectedOption]; 

        setOptions(updatedOptions);
    };

    return (
        <div>
            <TextControl
                label="Selected Models"
                value={model.join(', ')} 
                disabled
                placeholder='Selecteer functionaliteiten...'
            />
            <CheckboxControl
                label="Genereer Tekst"
                checked={options.includes('text_generate')}
                onChange={() => handleOptionToggle('text_generate')}
            />
            <CheckboxControl
                label="Genereer Afbeeldingen"
                checked={options.includes('image_generate')}
                onChange={() => handleOptionToggle('image_generate')}
            />
            <CheckboxControl
                label="Genereer Spraak"
                checked={options.includes('voice_generate')}
                onChange={() => handleOptionToggle('voice_generate')}
            />
        </div>
    );
};

export default OptionsModelSelector;