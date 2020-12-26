<?php

namespace Mollie\Form;

use Symfony\Component\Form\FormView;

interface FormInterface
{
	/**
	 * Returns whether the form is submitted.
	 *
	 * @return bool true if the form is submitted, false otherwise
	 */
	public function isSubmitted();

	/**
	 * Returns whether the form and all children are valid.
	 *
	 * If the form is not submitted, this method always returns false (but will throw an exception in 4.0).
	 *
	 * @return bool
	 */
	public function isValid();

	/**
	 * For legacy forms this does nothing. (Just having same path of execution like Symfony)
	 *
	 * @param mixed $request the request to handle
	 *
	 * @return $this
	 */
	public function handleRequest($request);

	/**
	 * @return array
	 */
	public function getData();

	/**
	 * @param array $value
	 */
	public function setData($value);

	/**
	 * @return FormView|string legacy forms return parsed view
	 */
	public function createView();
}
