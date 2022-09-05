// Type definitions for growl
// TypeScript Version: 3.9

/// <reference types="jquery" />

declare namespace JQueryGrowl {
    interface JQueryGrowlInterface {
        error(title: string, message: string);
        message(title: string, message: string);
    }
}

interface JQueryStatic {
    growl: JQueryGrowl.JQueryGrowlInterface;
}
