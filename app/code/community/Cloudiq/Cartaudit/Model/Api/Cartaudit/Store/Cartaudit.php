<?php

class Cloudiq_Cartaudit_Model_Api_Cartaudit_Store_Cartaudit extends Cloudiq_Core_Model_Api_Abstract {

    protected $response_error_message;

    /**
     * Create a cloud.IQ API request for the "store cartaudit" function from the given config.
     *
     * @param array $arguments Array of arguments, must contain an instance of Cloudiq_Cartaudit_Model_Config
     *                         as the 'config' argument.
     * @return Cloudiq_Core_Model_Api_Request
     */
    public function buildRequest($arguments) {
        if (!array_key_exists('config', $arguments) || ! ($arguments['config'] instanceof Cloudiq_Cartaudit_Model_Config)) {
            return null;
        }

        $parameters = $this->_convertConfigToParameters($arguments['config']);
        $parameters['mode'] = 'store';
        $parameters['action'] = 'cartaudit';

        /** @var Cloudiq_Core_Model_Api_Request $request */
        $request = $this->_getRequestObject()->setParameters($parameters);

        return $request;
    }

    /**
     * Check if the given cloud.IQ API response is a successful cartAudit response.
     *
     * @param Cloudiq_Core_Model_Api_Response $response
     *
     * @return boolean
     */
    public function isResponseSuccessful($response) {
        $this->response_error_message = null;

        // Check if we have a response
        if (!$response) {
            $this->response_error_message = "No API response received.";
            return false;
        }

        // Check if the response was successful
        if (!$response->wasSuccessful()) {
            $this->response_error_message = $response->getErrorMessage();
            return false;
        }

        // Check if a cartAudit response was returned
        $cartaudit_response = $response->getResponse()->cartAudit;
        if (is_null($cartaudit_response)) {
            $this->response_error_message = "Unknown cartAudit response status.";
            return false;
        }

        // Check if the cartAudit response was successful
        if ($cartaudit_response['status'] != Cloudiq_Core_Model_Api_Response::STATUS_SUCCESS) {
            if ($cartaudit_response->errorMessages) {
                $this->response_error_message = sprintf("%s", $cartaudit_response->errorMessages);
            } else {
                $this->response_error_message = $response->getApiStatusCodeDescription($cartaudit_response['status']);
            }
            return false;
        }

        // All checks passed
        return true;
    }

    /**
     * Return the error message generated by the last call to isResponseSuccessful().
     *
     * @return string
     */
    public function getResponseErrorMessage() {
        return $this->response_error_message;
    }

    /**
     * Create an array of parameters used by the "store cartaudit" API function from the provided config.
     *
     * @param Cloudiq_Cartaudit_Model_Config $config
     * @return array
     */
    protected function _convertConfigToParameters(Cloudiq_Cartaudit_Model_Config $config) {
        $parameters = $config->toCamelCaseArray();

        // Remove parameters not used by the API
        unset(
            $parameters["enabled"],
            $parameters["cloudiqTag"]
        );

        return $parameters;
    }
}
